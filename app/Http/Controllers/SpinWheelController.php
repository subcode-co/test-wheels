<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\Spin;
use App\Models\SpinParticipant;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SpinWheelController extends Controller
{
    /**
     * Show spin wheel landing (phone form, or result if just saved).
     */
    public function index(Request $request)
    {
        $prizes = Prize::active()->ordered()->get();
        $wheelItems = $prizes->map(fn (Prize $p) => [
            'id' => $p->id,
            'label' => $p->name,
            'backgroundColor' => $p->color ?: $this->defaultColor($p->id),
            'labelColor' => '#fff',
            'probability_weight' => $p->probability_weight,
            'is_winner' => $p->is_winner,
        ])->values()->all();

        // إجمالي الفائزين = عدد المحاولات الناجحة المحفوظة في قاعدة البيانات (جدول spins)
        $winnersCount = Spin::count();

        $result = session('spin_result');

        return view('spin-wheel', [
            'wheelItems' => $wheelItems,
            'winnersCount' => $winnersCount,
            'result' => $result,
            'whatsappNumber' => config('services.whatsapp.number', '905357176133'),
        ]);
    }

    /**
     * Register phone and allow one spin (one attempt per phone).
     */
    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'regex:/^05\d{8}$/'],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }
            return back()->withErrors($validator)->withInput();
        }

        $phone = preg_replace('/\D/', '', $request->input('phone'));

        // رقم الهاتف فريد: كل رقم = محاولة واحدة فقط
        $existing = SpinParticipant::query()
            ->where('phone', $phone)
            ->first();

        if ($existing && $existing->hasSpun()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'هذا الرقم استخدم محاولته بالفعل. محاولة واحدة فقط لكل رقم.',
                ], 422);
            }
            return back()->with('error', 'هذا الرقم استخدم محاولته بالفعل. محاولة واحدة فقط لكل رقم.');
        }

        if ($existing) {
            $participant = $existing;
        } else {
            try {
                $participant = SpinParticipant::create([
                    'phone' => $phone,
                    'country_code' => '',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // تم التسجيل بنفس الرقم من جهاز آخر في نفس اللحظة
                $participant = SpinParticipant::where('phone', $phone)->first();
                if (!$participant || $participant->hasSpun()) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'message' => 'هذا الرقم استخدم محاولته بالفعل. محاولة واحدة فقط لكل رقم.',
                        ], 422);
                    }
                    return back()->with('error', 'هذا الرقم استخدم محاولته بالفعل. محاولة واحدة فقط لكل رقم.');
                }
            }
        }

        session([
            'spin_participant_id' => $participant->id,
            'spin_phone_display' => $phone,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('spin-wheel.index');
    }

    /**
     * Save spin result (one spin per participant; prize_id from client).
     */
    public function saveResult(Request $request)
    {
        $participantId = session('spin_participant_id');
        if (!$participantId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'انتهت الجلسة. يرجى إدخال رقمك من البداية.'], 403);
            }
            return redirect()->route('spin-wheel.index')->with('error', 'انتهت الجلسة. يرجى إدخال رقمك من البداية.');
        }

        $validator = Validator::make($request->all(), [
            'prize_id' => ['required', 'integer', 'exists:prizes,id'],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }
            return redirect()->route('spin-wheel.index')->with('error', 'بيانات غير صالحة.');
        }

        $participant = SpinParticipant::find($participantId);
        if (!$participant || $participant->hasSpun()) {
            session()->forget(['spin_participant_id', 'spin_phone_display']);
            if ($request->wantsJson()) {
                return response()->json(['message' => 'تم استخدام المحاولة مسبقاً.'], 422);
            }
            return redirect()->route('spin-wheel.index')->with('error', 'تم استخدام المحاولة مسبقاً.');
        }

        // حفظ نتيجة العجلة في قاعدة البيانات (جدول spins)
        $prize = Prize::findOrFail($request->input('prize_id'));
        Spin::create([
            'spin_participant_id' => $participant->id,
            'prize_id' => $prize->id,
        ]);

        $result = [
            'prize_id' => $prize->id,
            'prize_name' => $prize->name,
            'phone_display' => session('spin_phone_display', $participant->phone),
        ];

        session([
            'spin_result' => $result,
        ]);
        session()->forget(['spin_participant_id', 'spin_phone_display']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'result' => $result]);
        }

        return redirect()->route('spin-wheel.index')->with('success', true);
    }

    private function defaultColor(int $index): string
    {
        $colors = ['#0d9488', '#2563eb', '#7c3aed', '#c026d3', '#dc2626', '#ea580c', '#ca8a04', '#16a34a'];
        return $colors[$index % count($colors)];
    }
}

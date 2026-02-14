@extends('layouts.app')

@section('content')
<div class="game-container spin-wheel-app" id="spinWheelApp">
    {{-- 1️⃣ Phone Number Entry Page --}}
    <section id="phoneSection" class="spin-screen spin-screen-phone">
        <div class="auth-box-premium">
            <div class="auth-box-top">
                <div class="auth-header">
                    <div class="logo-container">
                        <img src="{{ asset('images/noktaclinic1.png') }}" alt="Logo" class="auth-logo" />
                    </div>
                    <h1 class="glow-text display-5 mt-3 text-danger"><span>🎡</span> دولاب الحظ</h1>
                    <p class="subtitle-auth"> سجل رقمك لتجربة حظك اليوم </p>
                </div>

                <div class="form-container-premium">
                    <div class="form-group-premium">
                        <label class="form-label-premium">رقم الهاتف</label>
                        <input
                            id="phoneInput"
                            type="tel"
                            placeholder="05xxxxxxxx"
                            class="game-input-premium"
                            maxlength="15"
                            dir="ltr"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false"
                            inputmode="numeric"
                            name="phone_number"
                        />
                        <div class="input-hint">أدخل رقم الهاتف (يبدأ بـ 05 ويتكون من 10 أرقام)</div>
                        <small id="phoneError" class="error-message-premium d-block mt-3" style="display: none !important;"></small>
                        <div id="phoneSuccess" class="success-message-premium d-block mt-3" style="display: none !important;">✅ الرقم صحيح</div>
                    </div>

                    <p class="one-attempt-message">لديك محاولة واحدة فقط — لا إعادة ولا تغيير</p>

                    <button id="startGameBtn" type="button" class="btn-primary-game-premium mt-4" disabled>
                        <span class="btn-icon">🎰</span>
                        <span class="btn-text">ابدأ الآن</span>
                        <span class="btn-arrow">←</span>
                    </button>
                </div>
            </div>

            <div class="auth-footer">
                <div class="auth-footer-divider"></div>
                <p class="warning-text">
                    <span class="warning-icon" aria-hidden="true"></span>
                    محاولة واحدة فقط لكل رقم — لا إعادة للمحاولة
                </p>
            </div>
        </div>
    </section>

    {{-- 2️⃣ Spin Wheel Page (شكل العجلة + بيانات المشاركة) --}}
    <section id="wheelSection" class="spin-screen spin-screen-wheel" style="display: none;">
        <div class="wheel-layout">
            <div class="wheel-col wheel-col-main">
                <div class="wheel-main-area">
                    <div class="wheel-stage">
                        <div class="luxury-pointer"></div>
                        <div id="wheelEl" class="canvas-holder"></div>
                        <div id="logoRef" class="wheel-center-logo">
                            <img src="{{ asset('images/noktaclinic1.png') }}" alt="Logo" />
                        </div>
                    </div>
                    <button id="spinBtn" class="btn-spin-luxury" type="button">
                        <span id="spinBtnText">🎰 اضغط للربح</span>
                    </button>
                </div>
            </div>
            <div class="wheel-col wheel-col-sidebar">
                <div class="glass-card stats-sidebar">
                    <h3 class="side-title">📊 بيانات المشاركة</h3>
                    <div class="stat-item">
                        <label>📱 رقمك المسجل:</label>
                        <span id="userPhoneDisplay"></span>
                    </div>
                    <div class="stat-item">
                        <label>🎡 عدد اللفات:</label>
                        <span id="spinsCountDisplay">0</span>
                    </div>
                    <div class="stat-item highlight">
                        <label>🏆 إجمالي الفائزين (من عدد المحاولات الناجحة):</label>
                        <span id="winnersCountDisplay">{{ $winnersCount ?? 0 }}</span>
                    </div>
                    <div class="alert-info-game">💡 ملاحظة: لديك محاولة واحدة فقط لكل رقم هاتف.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Hidden form for saving result --}}
    <form id="saveResultForm" action="{{ route('spin-wheel.save-result') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="prize_id" id="formPrizeId">
    </form>
</div>

 
 <div class="modal fade" id="resultModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content winner-modal">
            <div class="modal-body text-center px-5 py-6 position-relative">
                <button type="button" class="btn-close-modal" id="closeResultModalBtn" aria-label="إغلاق">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="confetti-icon" id="resultEmoji">🎉</div>
                <h2 class="win-title">فوز مستحق!</h2>
                <div class="prize-text-wrapper">
                    <div class="prize-display" id="resultDisplay"></div>
                </div>
                <p class="result-locked-msg">هذه النتيجة نهائية ولا يمكن تغييرها</p>
                <p class="phone-tag">
                    📱 سيتم إرسال الكود للرقم: <span class="phone-number" id="resultPhoneNumber"></span>
                </p>
              <button type="button" class="btn-claim" onclick="window.location='{{ url('spin-wheel') }}'">
                <span class="claim-text">استلام الآن</span>
                <span class="claim-arrow">←</span>
            </button>

            </div>
        </div>
    </div>
</div>
 @endsection

@push('scripts')
<script>
    window.wheelItemsFromBlade = @json($wheelItems ?? []);
    window.winnersCount = {{ $winnersCount ?? 0 }};
    window.whatsappNumber = @json($whatsappNumber ?? '905357176133');
    window.serverResult = @json($result ?? null);
    window.gameState = {
        userPhone: '',
        userPhoneDisplay: '',
        hasSpun: false,
        isSpinning: false,
        wheel: null,
        animationFrameId: null,
        prizes: [],
        lastWinner: null,
    };
</script>
@endpush

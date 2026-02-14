@extends('layouts.app')

@section('content')
<div class="game-container spin-wheel-app" id="spinWheelApp">
    {{-- 1️⃣ Phone Number Entry Page --}}
    <section id="phoneSection" class="spin-screen spin-screen-phone" style="{{ isset($result) ? 'display: none !important;' : '' }}">
        <div class="auth-box-premium">
            <div class="auth-box-top">
                <div class="auth-header">
                    <div class="logo-container">
                        <img src="{{ asset('images/noktaclinic1.png') }}" alt="Logo" class="auth-logo" />
                    </div>
                    <h1 class="glow-text display-5 mt-3 text-danger"><span>🎡</span> دولاب الحظ</h1>
                    <p class="subtitle-auth">أدخل رقم هاتفك للحصول على فرصة واحدة للفوز بمكافأة حقيقية</p>
                </div>

                <div class="form-container-premium">
                    <div class="form-group-premium">
                        <label class="form-label-premium">رقم الهاتف</label>
                        <input
                            id="phoneInput"
                            type="tel"
                            placeholder="05xxxxxxxx"
                            class="game-input-premium"
                            maxlength="10"
                            dir="ltr"
                        />
                        <div class="input-hint">أدخل رقم الهاتف (يبدأ بـ 05 ويتكون من 10 أرقام)</div>
                        <small id="phoneError" class="error-message-premium d-block mt-3" style="display: none !important;"></small>
                        <div id="phoneSuccess" class="success-message-premium d-block mt-3" style="display: none !important;">✅ الرقم صحيح</div>
                    </div>

                    <p class="one-attempt-message">لديك محاولة واحدة فقط — لا إعادة ولا تغيير</p>

                    <button id="startGameBtn" class="btn-primary-game-premium mt-4" disabled>
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

    {{-- 3️⃣ Result Page --}}
    <section id="resultSection" class="spin-screen spin-screen-result" style="{{ isset($result) ? 'display: flex !important;' : 'display: none;' }}">
        <div class="glass-card result-card">
            <div class="result-header">
                <div class="confetti-icon">🎉</div>
                <h2 class="result-title">مبروك!</h2>
            </div>
            <div class="prize-text-wrapper">
                <p class="result-prize-label">جائزتك:</p>
                <div class="prize-display" id="resultPrizeName">{{ $result['prize_name'] ?? '' }}</div>
            </div>
            <p class="result-locked-msg">هذه النتيجة نهائية ولا يمكن تغييرها</p>
            <p class="result-phone-tag">
                📱 الرقم المسجل: <strong id="resultPhoneDisplay">{{ $result['phone_display'] ?? '' }}</strong>
            </p>
            <div class="result-steps">
                <p class="result-steps-title">كيفية استلام الجائزة:</p>
                <ol class="result-steps-list">
                    <li>تواصل معنا عبر واتساب بالضغط على الزر أدناه</li>
                    <li>أرسل رقم هاتفك والجائزة التي فزت بها</li>
                    <li>سنؤكد لك موعد الاستلام أو التفاصيل</li>
                </ol>
            </div>
            <a id="whatsappBtn" href="#" target="_blank" rel="noopener" class="btn-whatsapp">
                <span class="whatsapp-icon">💬</span>
 
                 <span>أستلام الأن </span>
            </a>
        </div>
    </section>

    {{-- Hidden form for saving result --}}
    <form id="saveResultForm" action="{{ route('spin-wheel.save-result') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="prize_id" id="formPrizeId">
    </form>
</div>

@if(!isset($result))
{{-- In-page result modal with close button (no WhatsApp) --}}
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
                <button type="button" id="claimResultBtn" class="btn-claim">
                    <span class="claim-text">استلام الآن</span>
                    <span class="claim-arrow">←</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
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

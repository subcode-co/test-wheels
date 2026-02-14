<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دولاب الحظ - Spin Wheel</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/spin-wheel.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-navbar">
        <div class="top-navbar-inner">
            <div class="top-navbar-brand">
                <img src="{{ asset('images/noktaclinic1.png') }}" alt="مركز نقطة" class="nav-logo" />
                <div class="nav-brand-text">
                    <span class="nav-brand-title">مركز نقطة</span>
                    <span class="nav-brand-subtitle">دولاب الحظ</span>
                </div>
            </div>
            <div class="top-navbar-contact">
                {{-- <a href="{{ url('/admin') }}" class="nav-contact-item" title="لوحة التحكم">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                    لوحة التحكم
                </a> --}}
                <a href="mailto:Info@noktaclinic.com" class="nav-contact-item">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    Info@noktaclinic.com
                </a>
                <a href="tel:905357176133" class="nav-contact-item">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    905357176133
                </a>
            </div>
        </div>
    </nav>

    <div id="app">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom JS -->
    <script type="module" src="{{ asset('js/spin-wheel.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>
</html>

@extends('layouts.auth')

@section('title', 'تسجيل الدخول — Sir Sharaf Academy')

@section('side-content')
  <h2>اتكلم إنجلش… مش إنجليزي</h2>
  <p>
    ادخل على حسابك وتابع كورساتك، حضورك، وفواتيرك في مكان واحد.
    لو لسه معملتش حساب، تقدر تسجل من هنا في ثواني.
  </p>
@endsection

@section('side-badge')
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
  </svg>
  تسجيل دخول آمن ومشفّر
@endsection

@section('content')
  <h1>تسجيل الدخول</h1>
  <p class="sub">أهلاً بيك تاني، ادخل بياناتك عشان تكمّل</p>

  @if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="field">
      <label for="email">البريد الإلكتروني</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}"
             class="{{ $errors->has('email') ? 'is-invalid' : '' }}" autofocus required>
    </div>

    <div class="field">
      <label for="password">كلمة المرور</label>
      <input type="password" id="password" name="password" required>
    </div>

    <div class="field-row">
      <label class="checkbox-wrap">
        <input type="checkbox" name="remember"> تذكرني
      </label>
      {{-- @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" style="color:var(--red-600); text-decoration:none;">نسيت كلمة المرور؟</a>
      @endif --}}
    </div>

    <button type="submit" class="btn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/>
      </svg>
      دخول
    </button>
  </form>

  <div class="foot-link">
    مالكش حساب؟ <a href="{{ route('register') }}">سجّل دلوقتي</a>
  </div>

  <div class="foot-link">
    أنت مسؤول (Admin)؟ <a href="{{ route('admin.login') }}">ادخل من هنا</a>
  </div>
@endsection

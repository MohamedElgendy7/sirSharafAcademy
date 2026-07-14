@extends('layouts.auth')

@section('title', 'إنشاء حساب — Sir Sharaf Academy')

@section('side-content')
  <h2>ابدأ رحلتك دلوقتي</h2>
  <p>
    اعمل حسابك في دقيقة واحدة، وابدأ تتابع كورساتك، حضورك، وفواتيرك
    أول بأول من مكان واحد.
  </p>
@endsection

@section('side-badge')
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>
  </svg>
  حساب جديد لطلاب الأكاديمية
@endsection

@section('content')
  <h1>إنشاء حساب جديد</h1>
  <p class="sub">البيانات دي هتستخدم لتسجيل دخولك بعد كده</p>

  @if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="field">
      <label for="name">الاسم بالكامل</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}"
             class="{{ $errors->has('name') ? 'is-invalid' : '' }}" autofocus required>
    </div>

    <div class="field">
      <label for="email">البريد الإلكتروني</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}"
             class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
    </div>

    <div class="field">
      <label for="password">كلمة المرور</label>
      <input type="password" id="password" name="password"
             class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
    </div>

    <div class="field">
      <label for="password_confirmation">تأكيد كلمة المرور</label>
      <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn" style="margin-top:6px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>
      </svg>
      إنشاء الحساب
    </button>
  </form>

  <div class="foot-link">
    عندك حساب بالفعل؟ <a href="{{ route('login') }}">سجّل دخولك</a>
  </div>
@endsection

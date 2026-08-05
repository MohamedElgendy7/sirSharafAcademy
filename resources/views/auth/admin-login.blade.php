@extends('layouts.auth')

@section('title', 'دخول لوحة التحكم — Sir Sharaf Academy')

@section('side-content')
  <h2>لوحة تحكم الإدارة</h2>
  <p>
    الوصول ده مخصص لفريق الإدارة بس. من هنا تقدر تتابع الطلاب، الفروع،
    الفواتير، والتقارير الكاملة للأكاديمية.
  </p>
@endsection

@section('side-badge')
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z"/>
  </svg>
  دخول مخصص للإدارة فقط
@endsection

@section('content')
  <h1>دخول لوحة التحكم</h1>
  <p class="sub">من فضلك ادخل بيانات حساب الإدارة الخاص بيك</p>

  @if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.login') }}">
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
    </div>

    <button type="submit" class="btn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z"/>
      </svg>
      دخول لوحة التحكم
    </button>
  </form>

  <div class="foot-link">
    مش أدمن؟ <a href="{{ route('login') }}">ارجع لصفحة تسجيل دخول الطلاب</a>
  </div>
@endsection

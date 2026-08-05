@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <h4>أهلاً، {{ auth()->user()->name }} 👋</h4>
    <p class="text-muted">دي صفحتك الرئيسية. استخدم "امتحان" من القائمة على اليمين عشان تشوف امتحاناتك المفعّلة.</p>
@endsection

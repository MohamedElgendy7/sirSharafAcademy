@extends('layouts.app')

@section('title', 'مجموعة نسخ جديدة')

@section('content')
<style>
    .exam-form .form-control { border: 1px solid var(--red-600); border-radius: .5rem; }
    .exam-form .form-control:focus { border-color: var(--red-600); box-shadow: 0 0 0 .2rem rgba(216,31,38,.15); }
</style>

<div class="container" dir="rtl" style="max-width: 550px;">
    <h4 class="mb-3">مجموعة نسخ جديدة</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('exam-version-groups.store') }}" class="exam-form">
        @csrf

        <div class="mb-3">
            <label class="form-label">اسم المجموعة</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="مثلاً: امتحان تحديد مستوى American Accent" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary">إنشاء المجموعة</button>
        <a href="{{ route('exam-version-groups.index') }}" class="btn btn-outline-secondary">إلغاء</a>
    </form>
</div>
@endsection

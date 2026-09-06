@extends('layouts.app')

@section('title', $group->name)

@section('content')
<div class="container" dir="rtl" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">{{ $group->name }}</h4>
            <p class="text-muted mb-0" style="font-size: 13px;">{{ $group->exams->count() }} نسخة</p>
        </div>
        <a href="{{ route('exams.create', ['version_group_id' => $group->id]) }}" class="btn btn-primary">+ إضافة نسخة جديدة</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($group->exams->isEmpty())
        <div class="alert alert-secondary">لسه مفيش نسخ في المجموعة دي. ابدأ بإضافة أول نسخة.</div>
    @else
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>عنوان النسخة</th>
                    <th>عدد الأسئلة</th>
                    <th>تاريخ الإنشاء</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group->exams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->questions()->count() }}</td>
                        <td>{{ $exam->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('exam-version-groups.index') }}" class="btn btn-outline-secondary mt-2">رجوع لكل المجموعات</a>
</div>
@endsection

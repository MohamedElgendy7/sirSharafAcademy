@extends('layouts.app')

@section('title', 'مجموعات نسخ امتحان تحديد المستوى')

@section('content')
<div class="container" dir="rtl" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">مجموعات نسخ امتحان تحديد المستوى</h4>
        <a href="{{ route('exam-version-groups.create') }}" class="btn btn-primary">+ مجموعة جديدة</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($groups->isEmpty())
        <div class="alert alert-secondary">لسه مفيش مجموعات نسخ. ابدأ بإنشاء أول مجموعة.</div>
    @else
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>اسم المجموعة</th>
                    <th>عدد النسخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->exams_count }}</td>
                        <td>
                            <a href="{{ route('exam-version-groups.show', $group) }}" class="btn btn-sm btn-outline-primary">عرض النسخ</a>
                            <a href="{{ route('exam-version-groups.edit', $group) }}" class="btn btn-sm btn-outline-secondary">تعديل</a>
                            <form action="{{ route('exam-version-groups.destroy', $group) }}" method="POST" class="d-inline" onsubmit="return confirm('متأكد من حذف المجموعة؟ النسخ نفسها مش هتتمسح.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

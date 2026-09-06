@extends('layouts.app')

@section('title', 'مكتبة الفيديوهات والكتب')

@section('extra-styles')
<style>
    .exams-topbar{ margin-bottom: 22px; }
    .exams-panel{
        background: var(--paper-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px;
    }
    .exams-table-wrapper{ border-radius: 14px; overflow: hidden; border: 1px solid #000; }
    .exams-table{ width: 100%; border-collapse: collapse; font-size: 13.5px; background: transparent; }
    .exams-table thead th{
        background-color: var(--navy-900); color: var(--white); font-weight: 700;
        padding: 14px 22px; text-align: center; white-space: nowrap;
    }
    .exams-table th, .exams-table td{ border: 1px solid #000; padding: 14px 22px; text-align: center; vertical-align: middle; }
    .exams-table tbody tr:nth-of-type(odd){ background-color: rgba(11, 31, 69, 0.03); }
    .exams-table tbody tr:hover{ background-color: rgba(11, 31, 69, 0.06); }

    .btn-sm-filled{
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
        border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;
        border: none; font-family: inherit; color: #fff; text-decoration: none;
    }
    .btn-view{ background-color: var(--red-600); }
    .btn-view:hover{ background-color: var(--red-700); color: #fff; }
    .btn-delete{ background-color: var(--red-600); }
    .btn-delete:hover{ background-color: var(--red-700); color: #fff; }

    .alert-success-custom{
        background-color: #e6f7ee; border: 1px solid #1a9c5c; color: #0e5c37;
        padding: 10px 16px; border-radius: var(--radius); font-size: 13px; margin-bottom: 16px;
    }

    .badge-general{
        font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
        background:#fff3cd; color:#8a6d00;
    }
    .badge-type-video{ background:#e6f0ff; color:#1e4483; }
    .badge-type-book{ background:#f2f4f9; color:#5b6b8c; }
    .badge-type{ font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
</style>
@endsection

@section('content')
    <div class="topbar exams-topbar">
        <div>
            <h1>مكتبة الفيديوهات والكتب</h1>
            <p>كل المواد المتاحة للطلاب حسب الكورس والمستوى</p>
        </div>
        <div class="top-actions">
            <a href="{{ route('admin.materials.create') }}" class="btn btn-primary">+ إضافة مادة جديدة</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-custom">{{ session('success') }}</div>
    @endif

    <div class="exams-panel">
        <div class="exams-table-wrapper" style="overflow-x:auto;">
            <table class="exams-table">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>الكورس</th>
                        <th>المستوى</th>
                        <th>مضاف بواسطة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materials as $material)
                        <tr>
                            <td>{{ $material->title }}</td>
                            <td>
                                <span class="badge-type {{ $material->type === 'video' ? 'badge-type-video' : 'badge-type-book' }}">
                                    {{ $material->type === 'video' ? 'فيديو' : 'كتاب' }}
                                </span>
                            </td>
                            <td>
                                @if ($material->is_general)
                                    <span class="badge-general">عامة لكل الطلاب</span>
                                @else
                                    {{ $material->course->name ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $material->is_general ? '—' : ($material->level->name ?? '—') }}</td>
                            <td>{{ $material->creator->name ?? '—' }}</td>
                            <td>
                                <a href="{{ route('materials.stream', $material) }}" target="_blank" class="btn-sm-filled btn-view">عرض</a>
                                <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('متأكد من حذف المادة؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn-sm-filled btn-delete">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="color: var(--ink-500);">مفيش مواد مضافة لسه.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

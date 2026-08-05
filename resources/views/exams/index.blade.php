@extends('layouts.app')

@section('title', 'الامتحانات — Sir Sharaf Academy')

@section('extra-styles')
<style>
    /* ============ صفحة الامتحانات: تستخدم نفس متغيرات الموقع المعرّفة في layouts.app ============ */

    .exams-topbar{
        margin-bottom: 22px;
    }

    /* الكارت اللي بيحتوي التيبل - نفس أسلوب .panel المستخدم في باقي الموقع */
    .exams-panel{
        background: var(--paper-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px;
    }

    .exams-panel h3{
        font-size: 14.5px;
        font-weight: 800;
        margin-bottom: 14px;
        color: var(--ink-900);
    }

    /* التيبل - حواف مدورة من برة بس */
    .exams-table-wrapper{
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #000;
    }

    .exams-table{
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
        background: transparent;
    }

    /* هيدر التيبل بلون الموقع الكحلي */
    .exams-table thead th{
        background-color: var(--navy-900);
        color: var(--white);
        font-weight: 700;
        padding: 14px 22px;
        text-align: center;
        white-space: nowrap;
        line-height: 1.6;
    }

    /* بوردر أسود واضح لكل خلية + تباعد كافي عشان الخط يبان واضح */
    .exams-table th,
    .exams-table td{
        border: 1px solid #000;
        padding: 14px 22px;
        vertical-align: middle;
        text-align: center;
        line-height: 1.6;
    }

    .exams-table tbody tr:nth-of-type(odd){
        background-color: rgba(11, 31, 69, 0.03);
    }

    .exams-table tbody tr:hover{
        background-color: rgba(11, 31, 69, 0.06);
    }

    /* أزرار الإجراءات - خلفية ملونة مش outline */
    .btn-sm-filled{
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        font-family: inherit;
        color: #fff;
        text-decoration: none;
    }

    .btn-edit{
        background-color: var(--navy-900);
    }
    .btn-edit:hover{
        background-color: var(--navy-800);
        color: #fff;
    }

    .btn-duplicate{
        background-color: #6c757d;
    }
    .btn-duplicate:hover{
        background-color: #5a6268;
        color: #fff;
    }

    .btn-delete{
        background-color: var(--red-600);
    }
    .btn-delete:hover{
        background-color: var(--red-700);
        color: #fff;
    }

    .actions-cell{
        display: flex;
        gap: 6px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .alert-success-custom{
        background-color: #e6f7ee;
        border: 1px solid #1a9c5c;
        color: #0e5c37;
        padding: 10px 16px;
        border-radius: var(--radius);
        font-size: 13px;
        margin-bottom: 16px;
    }
</style>
@endsection

@section('content')
    <div class="topbar exams-topbar">
        <div>
            <h1>الامتحانات</h1>
            <p>بنك الأسئلة والامتحانات الخاصة بالأكاديمية</p>
        </div>
        <div class="top-actions">
            <a href="{{ route('exams.create') }}" class="btn btn-primary">+ امتحان جديد</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-custom">{{ session('success') }}</div>
    @endif

    <div class="exams-panel">
        <h3>بنك الأسئلة والامتحانات</h3>

        <div class="exams-table-wrapper" style="overflow-x:auto;">
            <table class="exams-table">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>الكورس</th>
                        <th>المستوى</th>
                        <th>النوع</th>
                        <th>عدد الأسئلة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($exams as $exam)
                        <tr>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->course }}</td>
                            <td>{{ $exam->level }}</td>
                            <td>{{ $exam->type === 'placement' ? 'تحديد مستوى' : 'عادي' }}</td>
                            <td>{{ $exam->questions_count }}</td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('exams.edit', $exam) }}" class="btn-sm-filled btn-edit">تعديل / الأسئلة</a>

                                    <form action="{{ route('exams.duplicate', $exam) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-sm-filled btn-duplicate">نسخ</button>
                                    </form>

                                    <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('متأكد من حذف الامتحان؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-sm-filled btn-delete">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="color: var(--ink-500);">مفيش امتحانات لسه.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

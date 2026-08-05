@extends('layouts.app')

@section('title', 'الامتحانات')

@section('content')

<style>
  .exams-student-page{
    box-sizing:border-box;
  }

  .exams-student-page .page-head{
    margin-bottom:clamp(14px, 2vh, 22px);
  }
  .exams-student-page .page-head h1{
    font-size:clamp(18px, 2vw, 24px);
    font-weight:800;
    color:var(--ink-900, #0e1930);
  }

  .exams-student-page .alert{
    border-radius:10px;
    font-size:13.5px;
  }

  /* الكارت الرئيسي - نفس أسلوب باقي صفحات الموقع: من غير بوردر كبير */
  .exams-panel{
    background:var(--paper-card, #fff);
    border-radius:var(--radius, 14px);
    padding:clamp(10px, 1.5vw, 14px);
    box-sizing:border-box;
  }

  /* حواف مدورة من برة بس، بوردر أسود واضح حوالين القايمة كلها */
  .exams-list{
    border:1px solid #000;
    border-radius:12px;
    overflow:hidden;
  }

  .exams-list .list-group-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:clamp(14px, 1.8vh, 18px) clamp(16px, 2vw, 22px);
    border:none;
    border-bottom:1px solid #000;
    background:var(--paper-card, #fff);
  }
  .exams-list .list-group-item:last-child{
    border-bottom:none;
  }
  .exams-list .list-group-item:nth-of-type(odd){
    background-color: rgba(11, 31, 69, 0.04);
  }

  .exams-list .exam-title{
    font-weight:800;
    color:var(--ink-900, #0e1930);
    font-size:14.5px;
  }
  .exams-list .exam-level{
    color:var(--ink-500, #5b6b8c);
    font-size:12px;
  }

  /* زرار "ابدأ/كمّل الامتحان" بلون الأحمر بتاعنا */
  .exams-list .btn-primary{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:7px 18px;
    font-size:13px;
    font-weight:700;
    border-radius:8px;
    background:var(--red-600, #d81f26);
    border:none;
    color:#fff;
    text-decoration:none;
    transition:opacity .15s linear;
  }
  .exams-list .btn-primary:hover{
    opacity:.85;
  }

  /* شارة الدرجة - كحلي بدل الأخضر الافتراضي */
  .exams-list .badge-score{
    background:var(--navy-900, #0b1f45);
    color:#fff;
    font-size:12.5px;
    font-weight:700;
    padding:6px 12px;
    border-radius:20px;
  }

  /* شارة "منتهي" */
  .exams-list .badge-finished{
    background:#6c757d;
    color:#fff;
    font-size:12.5px;
    font-weight:700;
    padding:6px 12px;
    border-radius:20px;
  }
</style>

<div class="exams-student-page" dir="rtl">
    <div class="page-head">
        <h1>الامتحانات</h1>
    </div>

    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if ($sessions->isEmpty())
        <div class="alert alert-light border">مفيش امتحانات متفعّلة ليك حاليًا.</div>
    @else
        <div class="exams-panel">
            <div class="exams-list">
                @foreach ($sessions as $session)
                    <div class="list-group-item">
                        <div>
                            <div class="exam-title">{{ $session->exam->title }}</div>
                            <small class="exam-level">مستوى {{ $session->exam->level }}</small>
                        </div>

                        <div>
                            @if ($session->status === 'active' && ! $session->submission)
                                <a href="{{ route('student.exam-sessions.code-entry', $session) }}" class="btn btn-primary">
                                    {{ $session->started_at ? 'كمّل الامتحان' : 'ابدأ الامتحان' }}
                                </a>
                            @elseif ($session->submission)
                                <span class="badge-score">
                                    درجتك: {{ $session->submission->score }} / {{ $session->submission->total_points }}
                                </span>
                            @else
                                <span class="badge-finished">منتهي</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

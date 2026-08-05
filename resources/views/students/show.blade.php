@extends('layouts.app')

@section('content')

<style>
  .student-page{
    height:100%;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    box-sizing:border-box;
  }

  .student-page .page-head{
    flex-shrink:0;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom:clamp(12px, 2vh, 22px);
  }
  .student-page .page-head h1{
    font-size:clamp(18px, 2vw, 24px);
    font-weight:800;
    color:var(--ink-900, #0e1930);
  }
  .student-page .page-head p{
    font-size:clamp(11px, 1vw, 13px);
    color:var(--ink-500, #5b6b8c);
    margin-top:4px;
  }

  .alert-success{
    flex-shrink:0;
    background:#eafbf1;
    color:#1a9c5c;
    border:1px solid #b9ecd0;
    border-radius:10px;
    padding:10px 16px;
    font-size:13px;
    font-weight:600;
    margin-bottom:14px;
  }

  .form-panel{
    flex:1;
    min-height:0;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    background:var(--paper-card, #fff);
    border:1px solid var(--border, #e3e7f2);
    border-radius:var(--radius, 14px);
    padding:clamp(16px, 2.5vw, 32px);
    box-sizing:border-box;
  }

  .profile-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));
    gap:clamp(10px, 2.2vh, 22px) clamp(10px, 1.8vw, 22px);
    align-content:center;
    flex:1;
    min-height:0;
  }
  .profile-field .lbl{
    font-size:clamp(12px, 0.95vw, 13.5px);
    font-weight:700;
    color:var(--ink-900, #0e1930);
    margin-bottom:6px;
  }
  .profile-field .val{
    font-size:clamp(13px, 1vw, 14.5px);
    font-weight:700;
    color:var(--ink-900, #0e1930);
    padding:clamp(9px, 1.2vh, 13px) 14px;
    border:1.5px solid #000;
    border-radius:10px;
    background:#fff;
    box-sizing:border-box;
    min-height:20px;
  }

  .form-actions{
    flex-shrink:0;
    display:flex;
    gap:10px;
    padding-top:clamp(10px, 2vh, 18px);
    border-top:1px solid var(--border, #e3e7f2);
  }
  .form-actions .btn{
    padding:clamp(10px, 1.4vh, 13px) 22px;
  }
  .notes-field{
    flex-shrink:0;
    margin-top:clamp(10px, 2vh, 18px);
  }
  .notes-field label{
    display:block;
    font-size:clamp(12px, 0.95vw, 13.5px);
    font-weight:700;
    color:var(--ink-900, #0e1930);
    margin-bottom:6px;
  }
  .notes-field textarea{
    width:100%;
    resize:none;
    padding:10px 14px;
    border:1.5px solid #000;
    border-radius:10px;
    font-family:inherit;
    font-size:clamp(13px, 1vw, 14.5px);
    color:var(--ink-900, #0e1930);
    box-sizing:border-box;
    height:clamp(50px, 8vh, 80px);
  }
  .notes-field textarea:focus{
    outline:none;
    border-color:var(--red-600, #d81f26);
  }

  .form-actions .btn-danger{
    background:#fff;
    color:var(--red-700, #b5171d);
    border:1.5px solid var(--red-600, #d81f26);
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>ملف الطالب</h1>
      <p>عرض بيانات طلب التسجيل (غير قابلة للتعديل)</p>
    </div>
    <a href="{{ route('students.pending') }}" class="btn btn-ghost">رجوع للقائمة</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="form-panel">

    <div class="profile-grid">

      <div class="profile-field">
        <div class="lbl">اسم الطالب</div>
        <div class="val">{{ $student->name }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">رقم الهاتف</div>
        <div class="val">{{ $student->phone }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">رقم الواتساب</div>
        <div class="val">{{ $student->whatsapp ?? '—' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">البريد الإلكتروني</div>
        <div class="val">{{ $student->email ?? '—' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">السن</div>
        <div class="val">{{ $student->age ?? '—' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">النوع</div>
        <div class="val">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">رقم ولي الأمر</div>
        <div class="val">{{ $student->guardian_phone ?? '—' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">الفرع</div>
        <div class="val">{{ $student->branch_label ?? '—' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">المستوى</div>
        <div class="val">{{ $student->level ?? '—' }}</div>
      </div>

      <div class="profile-field">
        <div class="lbl">الكورس المطلوب</div>
        <div class="val">{{ $student->course ?? '—' }}</div>
      </div>

    </div>

    <div class="notes-field">
      <label>ملاحظات على الطلب <span style="font-weight:500; color:var(--ink-500, #5b6b8c); font-size:11px;">(اختياري)</span></label>
      <form id="decision-form" method="POST" action="{{ route('students.approve', $student) }}">
        @csrf
        <textarea name="notes" form="decision-form" placeholder="اكتب أي ملاحظة على الطلب...">{{ old('notes', $student->notes) }}</textarea>
      </form>
    </div>

    <div class="form-actions">
      <button type="submit" form="decision-form" formaction="{{ route('students.approve', $student) }}" class="btn btn-primary">اعتماد التسجيل</button>
      <button type="submit" form="decision-form" formaction="{{ route('students.reject', $student) }}" class="btn btn-danger" onclick="return confirm('متأكد إنك عايز ترفض طلب الطالب ده؟')">رفض الطلب</button>
    </div>
  </div>
</div>

@endsection

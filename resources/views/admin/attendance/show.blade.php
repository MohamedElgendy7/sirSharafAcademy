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

  /* الكارت الرئيسي: من غير بوردر، زي باقي صفحات الموقع */
  .form-panel{
    flex:1;
    min-height:0;
    display:flex;
    flex-direction:column;
    background:var(--paper-card, #fff);
    border-radius:var(--radius, 14px);
    padding:clamp(16px, 2.5vw, 28px);
    box-sizing:border-box;
    gap:clamp(12px, 2vh, 20px);
  }

  .requests-list{
    flex:1;
    min-height:0;
    overflow-y:auto;
    border:1.5px solid #000;
    border-radius:12px;
  }
  .requests-table{
    width:100%;
    border-collapse:collapse;
  }

  /* هيدر التيبل بلون الموقع الكحلي */
  .requests-table thead th{
    position:sticky;
    top:0;
    background:var(--navy-900, #0b1f45);
    text-align:right;
    font-size:clamp(11px, 0.85vw, 12.5px);
    font-weight:700;
    color:#fff;
    padding:clamp(10px, 1.4vh, 14px) clamp(14px, 1.8vw, 20px);
    border:1px solid #000;
  }
  .requests-table tbody td{
    padding:clamp(10px, 1.4vh, 14px) clamp(14px, 1.8vw, 20px);
    border:1px solid #000;
    font-size:clamp(13px, 1vw, 14px);
    color:var(--ink-900, #0e1930);
  }
  .requests-table tbody tr:nth-of-type(odd){
    background-color: rgba(11, 31, 69, 0.04);
  }

  /* اسم الطالب في نص الخانة بحجم واضح */
  .requests-table tbody td.name-cell{
    font-weight:800;
    text-align:center;
    font-size:15px;
  }

  .status-options{
    display:flex;
    width:100%;
  }

  /* Segmented Switch بدل الراديو بوتون العادي - بياخد عرض الخانة كلها */
  .status-toggle{
    display:flex;
    width:100%;
    border:1px solid #000;
    border-radius:10px;
    overflow:hidden;
  }
  .status-toggle label{
    position:relative;
    display:flex;
    flex:1;
    align-items:center;
    justify-content:center;
    padding:8px 12px;
    font-size:12.5px;
    font-weight:700;
    cursor:pointer;
    color:var(--ink-500, #5b6b8c);
    background:#fff;
    transition:background .15s ease, color .15s ease;
    border-inline-start:1px solid #000;
    margin:0;
  }
  .status-toggle label:first-child{
    border-inline-start:none;
  }
  .status-toggle input[type="radio"]{
    position:absolute;
    width:0;
    height:0;
    opacity:0;
    pointer-events:none;
  }
  .status-toggle label:has(input:checked){
    color:#fff;
  }
  .status-toggle label.status-present:has(input:checked){
    background:#1a9c5c;
  }
  .status-toggle label.status-late:has(input:checked){
    background:#e08c00;
  }
  .status-toggle label.status-absent:has(input:checked){
    background:var(--red-600, #d81f26);
  }
  .status-toggle label:hover{
    background:rgba(11, 31, 69, 0.06);
  }
  .status-toggle label:has(input:checked):hover{
    filter:brightness(0.95);
  }

  .form-actions{
    flex-shrink:0;
    display:flex;
    justify-content:flex-end;
    gap:10px;
  }

  /* زرار حفظ التعديلات بالأحمر بتاعنا */
  .form-actions .btn-primary,
  .form-actions .btn-primary:hover,
  .form-actions .btn-primary:active,
  .form-actions .btn-primary:visited{
    background-color:var(--red-600, #d81f26) !important;
    border:none !important;
    color:#fff !important;
    font-weight:700;
    padding:9px 20px;
    border-radius:10px;
    transition:opacity .15s linear;
  }
  .form-actions .btn-primary:hover{
    opacity:.85;
  }

  /* زرار رجوع للسجل بالأحمر بتاعنا */
  .page-head .btn-ghost,
  .page-head .btn-ghost:hover,
  .page-head .btn-ghost:active,
  .page-head .btn-ghost:visited{
    background-color:var(--red-600, #d81f26) !important;
    border:none !important;
    color:#fff !important;
    font-weight:700;
    padding:9px 20px;
    border-radius:10px;
    transition:opacity .15s linear;
  }
  .page-head .btn-ghost:hover{
    opacity:.85;
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>حصة {{ $session->arabic_taken_at }} — {{ $session->group->name }}</h1>
      <p>تقدر تعدّل حالة أي طالب وتحفظ من غير قيود</p>
    </div>
    <a href="{{ route('attendance.sessions', $session->group) }}" class="btn btn-ghost">رجوع للسجل</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('attendance.sessions.update', $session) }}" class="form-panel">
    @csrf
    @method('PUT')

    <div class="requests-list">
      <table class="requests-table">
        <thead>
          <tr>
            <th>اسم الطالب</th>
            <th>الحالة</th>
          </tr>
        </thead>
        <tbody>
          @foreach($session->attendances as $attendance)
            <tr>
              <td class="name-cell">{{ $attendance->student->name }}</td>
              <td>
                <div class="status-options">
                  <div class="status-toggle">
                    <label class="status-present">
                      <input type="radio" name="attendance[{{ $attendance->student_id }}]" value="present" {{ $attendance->status == 'present' ? 'checked' : '' }}>
                      حاضر
                    </label>
                    <label class="status-late">
                      <input type="radio" name="attendance[{{ $attendance->student_id }}]" value="late" {{ $attendance->status == 'late' ? 'checked' : '' }}>
                      متأخر
                    </label>
                    <label class="status-absent">
                      <input type="radio" name="attendance[{{ $attendance->student_id }}]" value="absent" {{ $attendance->status == 'absent' ? 'checked' : '' }}>
                      غايب
                    </label>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </div>
  </form>
</div>

@endsection

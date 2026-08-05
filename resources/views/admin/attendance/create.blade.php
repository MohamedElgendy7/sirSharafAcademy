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

  /* هيدر التيبل بلون الموقع الكحلي - نفس باقي الصفحات */
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

  .empty-state{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--ink-500, #5b6b8c);
    font-size:13.5px;
  }

  .form-actions{
    flex-shrink:0;
    display:flex;
    justify-content:flex-end;
    gap:10px;
  }

  /* زرار حفظ الحضور بلون الأحمر بتاعنا بدل الأزرق الافتراضي */
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
  /* زرار رجوع بالأحمر بتاعنا */
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
      <h1>أخد حضور — {{ $group->name }}</h1>
      <p>{{ $group->course }} — مستوى {{ $group->level }}</p>
    </div>
    <a href="{{ route('attendance.index') }}" class="btn btn-ghost">رجوع</a>
  </div>

  <form method="POST" action="{{ route('attendance.store', $group) }}" class="form-panel">
    @csrf

    @if($students->count())
      <div class="requests-list">
        <table class="requests-table">
          <thead>
            <tr>
              <th>اسم الطالب</th>
              <th>الحالة</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $student)
              <tr>
                <td class="name-cell">{{ $student->name }}</td>
                <td>
                  <div class="status-options">
                    <div class="status-toggle">
                      <label class="status-present">
                        <input type="radio" name="attendance[{{ $student->id }}]" value="present" checked>
                        حاضر
                      </label>
                      <label class="status-late">
                        <input type="radio" name="attendance[{{ $student->id }}]" value="late">
                        متأخر
                      </label>
                      <label class="status-absent">
                        <input type="radio" name="attendance[{{ $student->id }}]" value="absent">
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
        <button type="submit" class="btn btn-primary">حفظ الحضور</button>
      </div>
    @else
      <div class="empty-state">لا يوجد طلاب نشطين في الجروب ده</div>
    @endif

  </form>
</div>

@endsection
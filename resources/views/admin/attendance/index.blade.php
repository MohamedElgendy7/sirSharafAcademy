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

  /* هيدر التيبل بلون الموقع الكحلي - نفس باقي الصفحات */
  .requests-table thead th{
    position:sticky;
    top:0;
    background:var(--navy-900, #0b1f45);
    text-align:center;
    font-size:clamp(12px, 0.95vw, 14px);
    font-weight:700;
    color:#fff;
    padding:clamp(10px, 1.4vh, 14px) clamp(14px, 1.8vw, 20px);
    border:1px solid #000;
  }
  .requests-table tbody td{
    padding:clamp(10px, 1.4vh, 14px) clamp(14px, 1.8vw, 20px);
    border:1px solid #000;
    font-size:clamp(16px, 1.3vw, 18px);
    color:var(--ink-900, #0e1930);
    text-align:center;
  }
  .requests-table tbody tr:nth-of-type(odd){
    background-color: rgba(11, 31, 69, 0.04);
  }
  .requests-table tbody td.name-cell{ font-weight:800; }
  .requests-table tbody td.action-cell{ text-align:center; white-space:nowrap; }
  .requests-table tbody td.action-cell .btn{
    display:inline-flex;
    justify-content:center;
    width:110px;
  }
  .requests-table tbody td.action-cell .btn + .btn{
    margin-inline-start:8px;
  }

  .requests-table .btn{
    border:none;
    text-decoration:none;
    opacity:1;
    transition:opacity .05s linear;
  }
  .requests-table .btn:hover{
    opacity:.75;
  }
  .requests-table .btn:active{
    opacity:.6;
  }
  .requests-table .btn-primary,
  .requests-table .btn-primary:hover,
  .requests-table .btn-primary:active,
  .requests-table .btn-primary:visited{
    background-color:var(--red-600, #d81f26) !important;
    color:#fff !important;
  }
  .btn-sidebar,
  .btn-sidebar:hover,
  .btn-sidebar:active,
  .btn-sidebar:visited{
    background-color:var(--ink-900, #0e1930) !important;
    color:#fff !important;
    border:none !important;
  }

  .empty-state{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--ink-500, #5b6b8c);
    font-size:13.5px;
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>الحضور والغياب</h1>
      <p>اختار الجروب عشان تاخد حضور حصة جديدة أو تراجع الحصص السابقة</p>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="form-panel">

    @if($groups->count())
      <div class="requests-list">
        <table class="requests-table">
          <thead>
            <tr>
              <th>اسم الجروب</th>
              <th>الكورس</th>
              <th>المستوى</th>
              <th>عدد الطلاب</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($groups as $group)
              <tr>
                <td class="name-cell">{{ $group->name }}</td>
                <td>{{ $group->course }}</td>
                <td>{{ $group->level }}</td>
                <td>{{ $group->students_count }} / {{ $group->capacity }}</td>
                <td class="action-cell">
                  <a href="{{ route('attendance.create', $group) }}" class="btn btn-primary">تسجيل حضور</a>
                  <a href="{{ route('attendance.sessions', $group) }}" class="btn btn-sidebar">السجل السابق</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="empty-state">لا يوجد جروبات نشطة حاليًا</div>
    @endif

  </div>
</div>

@endsection

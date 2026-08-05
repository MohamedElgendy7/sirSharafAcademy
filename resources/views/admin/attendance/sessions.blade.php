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
    text-align:center;
  }
  .requests-table tbody tr:nth-of-type(odd){
    background-color: rgba(11, 31, 69, 0.04);
  }

  /* اسم/تاريخ الحصة في نص الخانة بحجم واضح */
  .requests-table tbody td.name-cell{
    font-weight:800;
    text-align:center;
    font-size:15px;
  }

  .requests-table tbody td.action-cell{ text-align:center; }
  .requests-table tbody td.action-cell .btn{
    display:inline-flex;
    justify-content:center;
    white-space:nowrap;
    width:auto;
    min-width:100px;
  }

  /* أزرار الموقع بالأحمر بتاعنا بدل الأزرق الافتراضي */
  .btn-primary,
  .btn-primary:hover,
  .btn-primary:active,
  .btn-primary:visited{
    background-color:var(--red-600, #d81f26) !important;
    border:none !important;
    color:#fff !important;
    font-weight:700;
    transition:opacity .15s linear;
  }
  .btn-primary:hover{
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

  .empty-state{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--ink-500, #5b6b8c);
    font-size:13.5px;
  }

  .pagination-wrap{
    flex-shrink:0;
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>سجل الحضور — {{ $group->name }}</h1>
      <p>كل الحصص اللي اتاخد فيها حضور للجروب ده</p>
    </div>
    <div style="display:flex; gap:8px;">
      <a href="{{ route('attendance.create', $group) }}" class="btn btn-primary">+ حصة جديدة</a>
      <a href="{{ route('attendance.index') }}" class="btn btn-ghost">رجوع</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="form-panel">

    @if($sessions->count())
      <div class="requests-list">
        <table class="requests-table">
          <thead>
            <tr>
              <th>تاريخ الحصة</th>
              <th>عدد المسجل حضورهم</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($sessions as $session)
              <tr>
                <td class="name-cell">{{ $session->arabic_taken_at }}</td>
                <td>{{ $session->attendances_count }}</td>
                <td class="action-cell">
                  <a href="{{ route('attendance.sessions.show', $session) }}" class="btn btn-primary">عرض / تعديل</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="pagination-wrap">
        {{ $sessions->links() }}
      </div>
    @else
      <div class="empty-state">لسه مفيش حصص متسجلة للجروب ده</div>
    @endif

  </div>
</div>

@endsection

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

  /* الكارت الرئيسي: من غير بوردر */
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

  .search-bar{
    flex-shrink:0;
    display:flex;
    gap:10px;
  }
  .search-bar input{
    flex:1;
    max-width:360px;
    padding:clamp(9px, 1.2vh, 12px) 14px;
    border:1.5px solid var(--red-600, #d81f26);
    border-radius:10px;
    font-family:inherit;
    font-size:clamp(13px, 1vw, 14.5px);
    color:var(--ink-900, #0e1930);
  }
  .search-bar input:focus{
    outline:none;
    border-color:var(--red-700, #b5171d);
  }

  #resultsContainer{
    flex:1;
    min-height:0;
    display:flex;
    flex-direction:column;
  }

  /* حواف مدورة من برة بس - الجدول جواها بوردر أسود عادي بدون تدوير لكل خلية */
  .requests-list{
    flex:1;
    min-height:0;
    overflow-y:auto;
    overflow-x:hidden;
    border:1px solid #000;
    border-radius:12px;
    scrollbar-width:none; /* Firefox */
    -ms-overflow-style:none; /* IE/Edge legacy */
  }
  .requests-list::-webkit-scrollbar{ display:none; width:0; height:0; } /* Chrome/Safari/Edge */

  .requests-table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:0;
  }
  .requests-table.table-striped > tbody > tr:nth-of-type(odd) > td{
    background-color: rgba(11, 31, 69, 0.04);
  }

  /* هيدر التيبل بلون الموقع الكحلي */
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

  /* بوردر أسود واضح لكل خلية */
  .requests-table tbody td{
    padding:clamp(12px, 1.6vh, 16px) clamp(14px, 1.8vw, 20px);
    border:1px solid #000;
    font-size:clamp(16px, 1.3vw, 18px);
    color:var(--ink-900, #0e1930);
    text-align:center;
    line-height:1.6;
  }
  .requests-table tbody td.name-cell{ font-weight:800; }
  .requests-table tbody td.action-cell{ text-align:center; }

  /* زرار الإجراء: بدرجة الأحمر بتاعتنا */
  .requests-table .btn-primary{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:8px 20px;
    font-size:clamp(14px, 1.1vw, 15px);
    font-weight:700;
    border-radius:8px;
    background:var(--red-600, #d81f26);
    color:#fff;
    text-decoration:none;
    border:none;
    opacity:1;
    transition:opacity .15s linear;
  }
  .requests-table .btn-primary:hover{
    opacity:.8;
  }
  .requests-table .btn-primary:active{
    opacity:.65;
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
    <h1>طلبات تسجيل جديدة</h1>
    <p>مراجعة واعتماد طلبات التسجيل المقدّمة من الطلاب الجدد</p>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="form-panel">

    <form method="GET" action="{{ route('students.pending') }}" class="search-bar" id="searchForm">
      <input type="text" name="search" id="searchInput" value="{{ $search }}" placeholder="ابحث بالاسم أو رقم الهاتف" autocomplete="off">
      @if($search)
        <a href="{{ route('students.pending') }}" class="btn btn-ghost">مسح البحث</a>
      @endif
    </form>

    <div id="resultsContainer">
      @include('students.partials.pending-table', ['students' => $students])
    </div>

    <script>
      (function(){
        const input = document.getElementById('searchInput');
        const container = document.getElementById('resultsContainer');
        let debounceTimer;

        input.addEventListener('input', function(){
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(function(){
            const search = input.value;
            const url = new URL(window.location.href);
            url.searchParams.set('search', search);

            // نحدّث اللينك في المتصفح من غير reload
            window.history.replaceState({}, '', url);

            fetch(url, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(response){ return response.text(); })
            .then(function(html){
              container.innerHTML = html;
            })
            .catch(function(err){
              console.error('حصل خطأ أثناء البحث:', err);
            });
          }, 400);
        });
      })();
    </script>

  </div>
</div>

@endsection
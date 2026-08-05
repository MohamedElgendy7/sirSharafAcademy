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
    margin-bottom:clamp(12px, 2vh, 18px);
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
    margin-bottom:12px;
  }
  .alert-error{
    flex-shrink:0;
    background:#fdecec;
    color:#b5171d;
    border:1px solid #f5c2c2;
    border-radius:10px;
    padding:10px 16px;
    font-size:13px;
    font-weight:600;
    margin-bottom:12px;
  }

  .content-cols{
    flex:1;
    min-height:0;
    display:grid;
    grid-template-columns:1.4fr 1fr;
    gap:16px;
  }

  .panel-box{
    display:flex;
    flex-direction:column;
    min-height:0;
    background:var(--paper-card, #fff);
    border:1px solid var(--border, #e3e7f2);
    border-radius:var(--radius, 14px);
    padding:clamp(14px, 2vw, 22px);
    box-sizing:border-box;
    gap:12px;
  }
  .panel-box h3{
    flex-shrink:0;
    font-size:14.5px;
    font-weight:800;
    color:var(--ink-900, #0e1930);
  }
  .panel-box .meta{
    flex-shrink:0;
    font-size:12px;
    color:var(--ink-500, #5b6b8c);
  }

  .list-wrap{
    flex:1;
    min-height:0;
    overflow-y:auto;
    border:1.5px solid #000;
    border-radius:12px;
  }
  table.tbl{
    width:100%;
    border-collapse:collapse;
  }
  table.tbl thead th{
    position:sticky;
    top:0;
    background:var(--paper, #f2f4f9);
    text-align:right;
    font-size:11.5px;
    font-weight:700;
    color:var(--ink-500, #5b6b8c);
    padding:10px 14px;
    border:1px solid #000;
  }
  table.tbl tbody td{
    padding:9px 14px;
    border:1px solid #000;
    font-size:13px;
    color:var(--ink-900, #0e1930);
  }
  table.tbl tbody td.name-cell{ font-weight:800; }
  table.tbl tbody td.action-cell{ text-align:center; }

  .btn-danger-sm{
    background:var(--red-600, #d81f26);
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:8px;
    font-size:11.5px;
    font-weight:700;
    cursor:pointer;
    font-family:inherit;
    opacity:1;
    transition:opacity .05s linear;
  }
  .btn-danger-sm:hover{ opacity:.75; }
  .btn-danger-sm:active{ opacity:.6; }

  .page-head .btn-ghost,
  .panel-box .btn-primary{
    border:none;
    text-decoration:none;
    opacity:1;
    transition:opacity .05s linear;
  }
  .page-head .btn-ghost:hover,
  .panel-box .btn-primary:hover{ opacity:.75; }
  .page-head .btn-ghost:active,
  .panel-box .btn-primary:active{ opacity:.6; }
  .page-head .btn-ghost{
    background:#071838;
    color:#fff;
  }
  .panel-box .btn-primary{
    background:var(--red-600, #d81f26);
    color:#fff;
  }

  .search-bar{
    flex-shrink:0;
    display:flex;
    gap:8px;
  }
  .search-bar input{
    flex:1;
    padding:clamp(9px, 1.2vh, 12px) 14px;
    border:1.5px solid #000;
    border-radius:10px;
    font-family:inherit;
    font-size:clamp(13px, 1vw, 14.5px);
    color:var(--ink-900, #0e1930);
  }
  .search-bar input:focus{ outline:none; border-color:var(--red-600, #d81f26); }

  .empty-state{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--ink-500, #5b6b8c);
    font-size:12.5px;
    text-align:center;
    padding:10px;
  }

  @media (max-width: 900px){
    .content-cols{ grid-template-columns:1fr; }
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>{{ $group->name }}</h1>
      <p>{{ $group->course }} — مستوى {{ $group->level }} — {{ $group->students->count() }} / {{ $group->capacity }} طالب</p>
    </div>
    <a href="{{ route('groups.index') }}" class="btn btn-ghost">رجوع لكل الجروبات</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif

  <div class="content-cols">

    {{-- طلاب الجروب --}}
    <div class="panel-box">
      <h3>طلاب الجروب</h3>

      @if($group->students->count())
        <div class="list-wrap">
          <table class="tbl">
            <thead>
              <tr>
                <th>اسم الطالب</th>
                <th>رقم الهاتف</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($group->students as $student)
                <tr>
                  <td class="name-cell">{{ $student->name }}</td>
                  <td>{{ $student->phone }}</td>
                  <td class="action-cell">
                    <form method="POST" action="{{ route('groups.removeStudent', [$group, $student]) }}" onsubmit="return confirm('متأكد إنك عايز تشيل الطالب ده من الجروب؟')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-danger-sm">حذف</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="empty-state">لسه مفيش طلاب في الجروب ده</div>
      @endif
    </div>

    {{-- إضافة طالب --}}
    <div class="panel-box">
      <h3>إضافة طالب للجروب</h3>
      <div class="meta">ابحث بالاسم أو رقم الهاتف بين الطلاب الحاليين</div>

      <form method="GET" action="{{ route('groups.show', $group) }}" class="search-bar" id="addStudentSearchForm">
        <input type="text" name="search" id="addStudentSearchInput" value="{{ $search }}" placeholder="ابحث بالاسم أو رقم الهاتف" autocomplete="off">
        <button type="submit" class="btn btn-primary">بحث</button>
      </form>

      <div id="addStudentResults">
        @include('groups.partials.available-students', ['availableStudents' => $availableStudents ?? null, 'search' => $search, 'group' => $group])
      </div>

      <script>
        (function(){
          const input = document.getElementById('addStudentSearchInput');
          const container = document.getElementById('addStudentResults');
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
</div>

@endsection

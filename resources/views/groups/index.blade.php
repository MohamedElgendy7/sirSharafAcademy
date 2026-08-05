@extends('layouts.app')

@section('title', 'الجروبات — Sir Sharaf Academy')

@section('extra-styles')
<style>
    /* ============ نفس الـ CSS بالحرف من exams/index.blade.php ============ */

    .exams-topbar{
        margin-bottom: 22px;
    }

    .exams-panel{
        background: var(--paper-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 18px;
    }

    .exams-panel h3{
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 14px;
        color: var(--ink-900);
    }

    .exams-table-wrapper{
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #000;
    }

    .exams-table{
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        background: transparent;
    }

    .exams-table thead th{
        background-color: var(--navy-900);
        color: var(--white);
        font-weight: 700;
        padding: 14px 22px;
        text-align: center;
        white-space: nowrap;
        line-height: 1.6;
    }

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

    .btn-sm-filled{
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        font-family: inherit;
        color: #fff;
        text-decoration: none;
    }

    .btn-edit{ background-color: var(--navy-900); }
    .btn-edit:hover{ background-color: var(--navy-800); color: #fff; }

    .btn-view{ background-color: var(--red-600); }
    .btn-view:hover{ background-color: var(--red-700); color: #fff; }

    .btn-duplicate{ background-color: #6c757d; }
    .btn-duplicate:hover{ background-color: #5a6268; color: #fff; }

    .btn-delete{ background-color: var(--red-600); }
    .btn-delete:hover{ background-color: var(--red-700); color: #fff; }

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
        font-size: 14.5px;
        margin-bottom: 16px;
    }

    /* ============ إضافات خاصة بصفحة الجروبات بس (البحث + التاج + فاضي) ============ */

    .groups-search-bar{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-bottom: 16px;
    }
    .groups-search-bar input[type="text"]{
        flex:1;
        min-width:180px;
        max-width:360px;
        padding: 10px 14px;
        border: 1px solid #000;
        border-radius:10px;
        font-family:inherit;
        font-size:15px;
        color: var(--ink-900);
    }
    .groups-search-bar select{
        padding: 10px 14px;
        border: 1px solid #000;
        border-radius:10px;
        font-family:inherit;
        font-size:15px;
        color: var(--ink-900);
        background:#fff;
    }
    .groups-search-bar input[type="text"]:focus,
    .groups-search-bar select:focus{
        outline:none;
        border-color: var(--red-600);
    }

    .tag{
        font-size:13px;
        font-weight:700;
        padding:4px 12px;
        border-radius:20px;
        display:inline-block;
    }
    .tag.active{ background:#eafbf1; color:#1a9c5c; }
    .tag.finished{ background:#f2f4f9; color:#5b6b8c; }
</style>
@endsection

@section('content')
    <div class="topbar exams-topbar">
        <div>
            <h1>الجروبات</h1>
            <p>كل الجروبات الحالية وعدد الطلاب فيها</p>
        </div>
        <div class="top-actions">
            <a href="{{ route('groups.create') }}" class="btn btn-primary">+ جروب جديد</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success-custom">{{ session('success') }}</div>
    @endif

    <div class="exams-panel">
        <h3>كل الجروبات</h3>

        <form method="GET" action="{{ route('groups.index') }}" class="groups-search-bar" id="searchForm">
            <input type="text" name="search" id="searchInput" value="{{ $search }}" placeholder="ابحث باسم الجروب" autocomplete="off">

            <select name="course" id="courseFilter">
                <option value="">كل الكورسات</option>
                <option value="American Accent" @selected(($course ?? '') == 'American Accent')>American Accent</option>
                <option value="Business English" @selected(($course ?? '') == 'Business English')>Business English</option>
                <option value="General English" @selected(($course ?? '') == 'General English')>General English</option>
                <option value="Conversation" @selected(($course ?? '') == 'Conversation')>Conversation</option>
                <option value="IELTS preps" @selected(($course ?? '') == 'IELTS preps')>IELTS preps</option>
                <option value="TOEFL preps" @selected(($course ?? '') == 'TOEFL preps')>TOEFL preps</option>
            </select>

            <select name="level" id="levelFilter">
                <option value="">كل المستويات</option>
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected(($level ?? '') == $i)>{{ $i }}</option>
                @endfor
            </select>

            <button type="submit" class="btn btn-primary">بحث</button>
            @if($search || !empty($course) || !empty($level))
                <a href="{{ route('groups.index') }}" class="btn btn-ghost" id="clearSearchBtn">مسح البحث</a>
            @endif
        </form>

        <div id="resultsContainer">
            @include('groups.partials.groups-table', ['groups' => $groups])
        </div>
    </div>

    <script>
      (function(){
        const form = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const courseFilter = document.getElementById('courseFilter');
        const levelFilter = document.getElementById('levelFilter');
        const container = document.getElementById('resultsContainer');
        let debounceTimer;

        function buildUrl(){
          const url = new URL(form.action);
          if (searchInput.value) url.searchParams.set('search', searchInput.value); else url.searchParams.delete('search');
          if (courseFilter.value) url.searchParams.set('course', courseFilter.value); else url.searchParams.delete('course');
          if (levelFilter.value) url.searchParams.set('level', levelFilter.value); else url.searchParams.delete('level');
          return url;
        }

        function runSearch(){
          const url = buildUrl();
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
        }

        searchInput.addEventListener('input', function(){
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(runSearch, 400);
        });

        courseFilter.addEventListener('change', runSearch);
        levelFilter.addEventListener('change', runSearch);

        form.addEventListener('submit', function(e){
          e.preventDefault();
          clearTimeout(debounceTimer);
          runSearch();
        });

        const clearBtn = document.getElementById('clearSearchBtn');
        if (clearBtn){
          clearBtn.addEventListener('click', function(e){
            e.preventDefault();
            searchInput.value = '';
            courseFilter.value = '';
            levelFilter.value = '';
            runSearch();
          });
        }
      })();
    </script>
@endsection

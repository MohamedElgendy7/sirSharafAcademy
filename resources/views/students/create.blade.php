@extends('layouts.app')

@section('content')

<style>
  /* ============================================
     صفحة تسجيل طالب جديد - Full Screen / بدون سكرول
     ============================================ */

  .student-page{
    height:100%;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    padding:0;
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

  .form-panel{
    flex:1;
    min-height:0;
    display:flex;
    flex-direction:column;
    background:var(--paper-card, #fff);
    border:1px solid var(--border, #e3e7f2);
    border-radius:var(--radius, 14px);
    padding:clamp(16px, 2.5vw, 32px);
    box-sizing:border-box;
  }

  .student-form{
    flex:1;
    min-height:0;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    gap:clamp(10px, 2vh, 20px);
  }

  .fields-area{
    flex:1;
    min-height:0;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:clamp(10px, 2.2vh, 22px);
  }

  .form-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
    gap:clamp(10px, 1.8vw, 22px);
  }
  .form-grid.full-row{ grid-template-columns:1fr; }

  .field label{
    display:block;
    font-size:clamp(12px, 0.95vw, 13.5px);
    font-weight:700;
    color:var(--ink-900, #0e1930);
    margin-bottom:6px;
  }
  .field .hint{
    font-weight:500;
    color:var(--ink-500, #5b6b8c);
    font-size:11px;
  }
  .field input,
  .field select{
    width:100%;
    padding:clamp(9px, 1.2vh, 13px) 14px;
    border:1.5px solid #000;
    border-radius:10px;
    font-family:inherit;
    font-size:clamp(14px, 1.05vw, 15.5px);
    color:var(--ink-900, #0e1930);
    background:#fff;
    box-sizing:border-box;
  }
  .field input:focus,
  .field select:focus{
    outline:none;
    border-color:var(--red-600, #d81f26);
  }
  .field select:disabled{
    background:#f3f4f6;
    color:var(--ink-500, #5b6b8c);
    cursor:not-allowed;
  }
  .field .error{
    color:var(--red-700, #b5171d);
    font-size:11px;
    margin-top:4px;
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
    border:none;
    text-decoration:none;
    opacity:1;
    transition:opacity .05s linear;
  }
  .form-actions .btn:hover{
    opacity:.75;
  }
  .form-actions .btn:active{
    opacity:.6;
  }
  .form-actions .btn-primary{
    background:var(--red-600, #d81f26);
    color:#fff;
  }
  .form-actions .btn-ghost{
    background:#071838;
    color:#fff;
  }

  /* موبايل */
  @media (max-width: 560px){
    .form-grid{ grid-template-columns:1fr; }
    .fields-area{ overflow-y:auto; } /* أقصى حالات الأمان على شاشات صغيرة جدًا */
  }
</style>

<div class="student-page">

  <div class="page-head">
    <h1>تسجيل طالب جديد</h1>
    <p>البيانات دي هتضاف كـ "طلب تسجيل جديد" لحد ما يتم اعتمادها</p>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="form-panel">
    <form class="student-form" method="POST" action="{{ route('students.store') }}">
      @csrf

      <div class="fields-area">

        <div class="form-grid full-row">
          <div class="field">
            <label>اسم الطالب</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="الاسم بالكامل">
            @error('name') <div class="error">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label>الكورس المطلوب <span class="hint">(اختياري)</span></label>
            <select name="course" id="course-select">
              <option value="">اختر الكورس</option>
              @foreach ($courses as $course)
                <option value="{{ $course->name }}" data-course-id="{{ $course->id }}" @selected(old('course') == $course->name)>
                  {{ $course->name }}
                </option>
              @endforeach
            </select>
            @error('course') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="field">
            <label>المستوى</label>
            <select name="level" id="level-select" disabled>
              <option value="">اختر الكورس الأول</option>
            </select>
            @error('level') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="field">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="01xxxxxxxxx">
            @error('phone') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="field">
            <label>رقم الواتساب</label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="01xxxxxxxxx">
            @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label>السن</label>
            <input type="number" name="age" value="{{ old('age') }}" min="3" max="100">
            @error('age') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="field">
            <label>النوع</label>
            <select name="gender">
              <option value="">اختر</option>
              <option value="male" @selected(old('gender') == 'male')>ذكر</option>
              <option value="female" @selected(old('gender') == 'female')>أنثى</option>
            </select>
            @error('gender') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="field">
            <label>البريد الإلكتروني <span class="hint">(اختياري)</span></label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="example@mail.com">
            @error('email') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="field">
            <label>الفرع</label>
            <select name="branch">
              <option value="">اختر الفرع</option>
              <option value="cairo" @selected(old('branch') == 'cairo')>فرع القاهرة</option>
              <option value="tanta" @selected(old('branch') == 'tanta')>فرع طنطا</option>
              <option value="kafr_elsheikh" @selected(old('branch') == 'kafr_elsheikh')>فرع كفر الشيخ</option>
              <option value="online" @selected(old('branch') == 'online')>Online</option>
            </select>
            @error('branch') <div class="error">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label>رقم ولي الأمر</label>
            <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="01xxxxxxxxx">
            @error('guardian_phone') <div class="error">{{ $message }}</div> @enderror
          </div>
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">تسجيل الطلب</button>
        <button type="reset" class="btn btn-ghost">مسح الفورم</button>
      </div>
    </form>
  </div>

</div>

<script>
(function () {
    var APP_URL = "{{ rtrim(url('/'), '/') }}";
    var courseSelect = document.getElementById('course-select');
    var levelSelect = document.getElementById('level-select');
    var oldLevel = "{{ old('level') }}";

    function loadLevels(courseId, selectAfterLoad) {
        levelSelect.disabled = true;
        levelSelect.innerHTML = '<option value="">جاري التحميل...</option>';

        if (!courseId) {
            levelSelect.innerHTML = '<option value="">اختر الكورس الأول</option>';
            return;
        }

        fetch(APP_URL + '/courses/' + courseId + '/levels', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (levels) {
            levelSelect.innerHTML = '<option value="">اختر المستوى</option>';

            levels.forEach(function (level) {
                var opt = document.createElement('option');
                opt.value = level.name;
                opt.textContent = level.name;
                if (selectAfterLoad && level.name == selectAfterLoad) {
                    opt.selected = true;
                }
                levelSelect.appendChild(opt);
            });

            levelSelect.disabled = false;
        })
        .catch(function () {
            levelSelect.innerHTML = '<option value="">حصل خطأ في تحميل المستويات</option>';
        });
    }

    courseSelect.addEventListener('change', function () {
        var selectedOption = courseSelect.options[courseSelect.selectedIndex];
        var courseId = selectedOption ? selectedOption.getAttribute('data-course-id') : null;
        loadLevels(courseId, null);
    });

    // لو الفورم رجع بأخطاء فاليديشن (old values)، نعيد تحميل المستويات
    // الخاصة بالكورس اللي كان متحدد قبل كده تلقائيًا
    if (courseSelect.value) {
        var initialOption = courseSelect.options[courseSelect.selectedIndex];
        var initialCourseId = initialOption ? initialOption.getAttribute('data-course-id') : null;
        loadLevels(initialCourseId, oldLevel);
    }
})();
</script>

@endsection
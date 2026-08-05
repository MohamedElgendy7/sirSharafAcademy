@extends('layouts.app')

@section('title', 'امتحان جديد')

@section('content')
<style>
    .exam-form .form-control,
    .exam-form .form-select {
        border: 1px solid var(--red-600);
        border-radius: .5rem;
    }
    .exam-form .form-control:focus,
    .exam-form .form-select:focus {
        border-color: var(--red-600);
        box-shadow: 0 0 0 .2rem rgba(216,31,38,.15);
    }
    .exam-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem 1rem;
    }
    @media (max-width: 560px) {
        .exam-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container" dir="rtl" style="max-width: 750px;">
    <h4 class="mb-2">إنشاء امتحان جديد</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('exams.store') }}" class="exam-form">
        @csrf

        <div class="mb-3">
            <label class="form-label">عنوان الامتحان</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="exam-grid">
            <div id="course-field">
                <label class="form-label">الكورس</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="" disabled {{ old('course_id') ? '' : 'selected' }}>اختر الكورس</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="course-field">
                <label class="form-label">نوع الامتحان</label>
                <select name="type" id="type" class="form-select" >
                    <option value="regular">امتحان عادي</option>
                    <option value="placement">امتحان تحديد مستوى</option>
                </select>
            </div>

            <div id="level-field">
                <label class="form-label">المستوى</label>
                <select name="level_id" id="level_id" class="form-select" required {{ old('course_id') ? '' : 'disabled' }}>
                    <option value="" selected>اختار الكورس الأول</option>
                </select>
            </div>

            

            <div>
                <label class="form-label">نظام الدرجات</label>
                <select name="points_mode" id="points_mode" class="form-select">
                    <option value="uniform">كل الأسئلة بنفس الدرجة</option>
                    <option value="custom">درجة مختلفة لكل سؤال</option>
                </select>
            </div>

            <div id="uniform-points-field">
                <label class="form-label">الدرجة لكل سؤال</label>
                <input type="number" name="uniform_points" class="form-control" value="{{ old('uniform_points', 1) }}" min="1">
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">إنشاء ومتابعة إضافة الأسئلة</button>
    </form>
</div>

<script>
const levelsUrlTemplate = "{{ route('courses.levels', ':courseId') }}";

function toggleUniform() {
    const mode = document.getElementById('points_mode').value;
    document.getElementById('uniform-points-field').style.display = mode === 'uniform' ? 'block' : 'none';
}

document.getElementById('points_mode').addEventListener('change', toggleUniform);
toggleUniform();

function toggleCourseLevel() {
    const type = document.getElementById('type').value;
    const courseField = document.getElementById('course-field');
    const levelField = document.getElementById('level-field');
    const courseSelect = document.getElementById('course_id');
    const levelSelect = document.getElementById('level_id');
    const isPlacement = type === 'placement';

    courseField.style.display = isPlacement ? 'none' : 'block';
    levelField.style.display = isPlacement ? 'none' : 'block';

    courseSelect.disabled = isPlacement;
    courseSelect.required = !isPlacement;
    levelSelect.disabled = isPlacement;
    levelSelect.required = !isPlacement;

    if (isPlacement) {
        courseSelect.value = '';
        levelSelect.innerHTML = '<option value="" selected>اختار الكورس الأول</option>';
    }
}

document.getElementById('type').addEventListener('change', toggleCourseLevel);
toggleCourseLevel();

document.getElementById('course_id').addEventListener('change', function () {
    const courseId = this.value;
    const levelSelect = document.getElementById('level_id');

    levelSelect.innerHTML = '<option value="" selected>جاري التحميل...</option>';
    levelSelect.disabled = true;

    if (!courseId) return;

    const url = levelsUrlTemplate.replace(':courseId', courseId);

    fetch(url)
        .then(res => res.json())
        .then(levels => {
            levelSelect.innerHTML = '<option value="" disabled selected>اختر المستوى</option>';
            levels.forEach(level => {
                const option = document.createElement('option');
                option.value = level.id;
                option.textContent = level.name;
                levelSelect.appendChild(option);
            });
            levelSelect.disabled = false;
        })
        .catch(() => {
            levelSelect.innerHTML = '<option value="" selected>حصل خطأ، جرب تاني</option>';
        });
});
</script>
@endsection

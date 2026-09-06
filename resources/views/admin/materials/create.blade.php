@extends('layouts.app')

@section('title', 'إضافة مادة جديدة')

@section('extra-styles')
<style>
    .exams-panel{
        background: var(--paper-card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        max-width: 640px;
    }
    .form-group{ margin-bottom: 16px; }
    .form-group label{ display:block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--ink-900); }
    .form-group input[type="text"],
    .form-group select,
    .form-group textarea,
    .form-group input[type="file"]{
        width: 100%; padding: 9px 12px; border: 1.5px solid #000; border-radius: 8px;
        font-family: inherit; font-size: 13.5px;
    }
    .form-group .hint{ font-size: 11.5px; color: var(--ink-500); margin-top: 4px; }
    .checkbox-row{ display:flex; align-items:center; gap:8px; margin-bottom:16px; }
    .checkbox-row input{ width:18px; height:18px; }
    #course-level-fields.hidden{ display:none; }
</style>
@endsection

@section('content')
    <div class="topbar exams-topbar">
        <div>
            <h1>إضافة مادة جديدة</h1>
            <p>فيديو أو كتاب (PDF) لكورس ومستوى معينين، أو مادة عامة لكل الطلاب</p>
        </div>
        <div class="top-actions">
            <a href="{{ route('admin.materials.index') }}" class="btn btn-ghost">رجوع للقائمة</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="exams-panel">
        <form method="POST" action="{{ route('admin.materials.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>العنوان</label>
                <input type="text" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label>النوع</label>
                <select name="type" id="type-select" required>
                    <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>فيديو</option>
                    <option value="book" {{ old('type') == 'book' ? 'selected' : '' }}>كتاب (PDF)</option>
                </select>
            </div>

            <div class="checkbox-row">
                <input type="checkbox" name="is_general" id="is-general-checkbox" value="1" {{ old('is_general') ? 'checked' : '' }}>
                <label for="is-general-checkbox" style="margin:0;">مادة عامة (تظهر لكل الطلاب بغض النظر عن كورسهم/مستواهم)</label>
            </div>

            <div id="course-level-fields">
                <div class="form-group">
                    <label>الكورس</label>
                    <select name="course_id">
                        <option value="">اختر الكورس</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>المستوى</label>
                    <select name="level_id">
                        <option value="">اختر المستوى</option>
                        @foreach ($levels as $level)
                            <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>وصف (اختياري)</label>
                <textarea name="description" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label id="file-label">الملف</label>
                <input type="file" name="file" required>
                <div class="hint" id="file-hint">فيديو (mp4, mov, avi, wmv, mkv) — حتى 500 ميجا</div>
            </div>

            <button type="submit" class="btn btn-primary">حفظ المادة</button>
        </form>
    </div>

    <script>
    (function () {
        const typeSelect = document.getElementById('type-select');
        const fileHint = document.getElementById('file-hint');
        const fileLabel = document.getElementById('file-label');
        const isGeneralCheckbox = document.getElementById('is-general-checkbox');
        const courseLevelFields = document.getElementById('course-level-fields');

        function updateFileHint() {
            if (typeSelect.value === 'video') {
                fileLabel.textContent = 'ملف الفيديو';
                fileHint.textContent = 'فيديو (mp4, mov, avi, wmv, mkv) — حتى 500 ميجا';
            } else {
                fileLabel.textContent = 'ملف الكتاب';
                fileHint.textContent = 'ملف PDF — حتى 20 ميجا';
            }
        }

        function updateCourseLevelVisibility() {
            if (isGeneralCheckbox.checked) {
                courseLevelFields.classList.add('hidden');
            } else {
                courseLevelFields.classList.remove('hidden');
            }
        }

        typeSelect.addEventListener('change', updateFileHint);
        isGeneralCheckbox.addEventListener('change', updateCourseLevelVisibility);

        updateFileHint();
        updateCourseLevelVisibility();
    })();
    </script>
@endsection

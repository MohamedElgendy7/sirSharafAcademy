@extends('layouts.app')

@section('title', $exam->title)

@section('content')
<div class="container" dir="rtl" style="max-width: 800px;">

    <div id="flash-box" class="alert alert-success d-none"></div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $exam->title }}</h4>
        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-secondary">رجوع للقائمة</a>
    </div>

    {{-- بيانات الامتحان الأساسية --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('exams.update', $exam) }}">
                @csrf @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">عنوان الامتحان</label>
                        <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الكورس</label>
                        <select name="course" class="form-select" required>
                            @foreach (['American Accent', 'Business English', 'General English', 'Conversation', 'IELTS preps', 'TOEFL preps'] as $course)
                                <option value="{{ $course }}" {{ $exam->course === $course ? 'selected' : '' }}>{{ $course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المستوى</label>
                        <select name="level" class="form-select" required>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $exam->level === $i ? 'selected' : '' }}>مستوى {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">النوع</label>
                        <select name="type" class="form-select">
                            <option value="regular" {{ $exam->type === 'regular' ? 'selected' : '' }}>عادي</option>
                            <option value="placement" {{ $exam->type === 'placement' ? 'selected' : '' }}>تحديد مستوى</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نظام الدرجات</label>
                        <select name="points_mode" class="form-select">
                            <option value="uniform" {{ $exam->points_mode === 'uniform' ? 'selected' : '' }}>موحّدة</option>
                            <option value="custom" {{ $exam->points_mode === 'custom' ? 'selected' : '' }}>مخصصة لكل سؤال</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الدرجة الموحّدة (لو الوضع موحّد)</label>
                        <input type="number" name="uniform_points" class="form-control" value="{{ $exam->uniform_points }}" min="1">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">حفظ التعديلات</button>
            </form>
        </div>
    </div>

    {{-- تفعيل الامتحان لجروب --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6>تفعيل الامتحان</h6>

            @if ($groups->isEmpty())
                <div class="alert alert-warning mb-0">
                    مفيش جروبات نشطة (status = active) حاليًا، لازم يكون عندك جروب نشط الأول عشان تقدر تفعّل الامتحان له.
                </div>
            @else
                <div class="d-flex gap-2">
                    <select id="group-select" class="form-select">
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="go-to-manage-btn" class="btn btn-success text-nowrap">اذهب لصفحة التفعيل</button>
                </div>
            @endif
        </div>
    </div>

    {{-- الأسئلة --}}
    <h5 class="mb-3">الأسئلة (<span id="questions-count">{{ $exam->questions->count() }}</span>)</h5>

    <div id="questions-list">
        @foreach ($exam->questions as $index => $question)
            <div class="exam-question-card mb-3" data-question-id="{{ $question->id }}">
                <div class="eqc-stripe"></div>
                <div class="eqc-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="q-number-badge">{{ $index + 1 }}</span>
                            <div class="fw-bold question-text">{{ $question->question_text }}</div>
                            <span class="badge bg-light text-dark">{{ $question->points }} درجة</span>
                        </div>
                        <button type="button" class="btn-icon-danger"
                                onclick="deleteQuestionHandler({{ $question->id }}, this)" title="حذف السؤال">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        </button>
                    </div>

                    @if ($question->attachment_path)
                        <div class="my-2">
                            @if ($question->attachment_type === 'image')
                                <img src="{{ $question->attachment_url }}" style="max-height:150px;" class="rounded">
                            @elseif ($question->attachment_type === 'audio')
                                <audio controls src="{{ $question->attachment_url }}"></audio>
                            @elseif ($question->attachment_type === 'video')
                                <video controls src="{{ $question->attachment_url }}" style="max-height:150px;"></video>
                            @endif
                        </div>
                    @endif

                    <div class="choices-list">
                        @foreach ($question->choices as $choice)
                            <div class="choice-chip {{ $choice->is_correct ? 'correct' : '' }}" data-choice-id="{{ $choice->id }}">
                                <span class="choice-chip-text">{{ $choice->choice_text }}</span>
                                <span class="d-flex align-items-center gap-2">
                                    <span class="correct-mark">
                                        @if ($choice->is_correct)
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                        @endif
                                    </span>
                                    <button type="button" class="btn-icon-muted"
                                            onclick="deleteChoiceHandler({{ $choice->id }}, this)" title="حذف">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                    </button>
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="add-choice-error alert alert-danger d-none mt-2"></div>

                    <form class="add-choice-form d-flex gap-2 mt-3" data-question-id="{{ $question->id }}">
                        <input type="text" name="choice_text" class="form-control form-control-sm" placeholder="نص اختيار جديد" required>
                        <div class="correct-checkbox-wrap">
                            <input type="checkbox" name="is_correct" value="1" id="correct_new_{{ $question->id }}">
                            <label class="small mb-0" for="correct_new_{{ $question->id }}">صحيح</label>
                        </div>
                        <button type="submit" class="btn-add-choice text-nowrap">+ إضافة اختيار</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- إضافة سؤال جديد --}}
    <div class="card">
        <div class="card-body">
            <h6>إضافة سؤال جديد</h6>
            <div id="add-question-error" class="alert alert-danger d-none"></div>

            <form id="add-question-form" enctype="multipart/form-data">
                <div class="d-flex gap-2 mb-2 align-items-start">
                    <div class="flex-grow-1">
                        <label class="form-label small mb-1">نص السؤال</label>
                        <textarea name="question_text" class="form-control" placeholder="نص السؤال" required></textarea>
                    </div>
                    <div style="max-width: 90px;">
                        <label class="form-label small mb-1">درجة السؤال</label>
                        <input type="number" name="points" class="form-control points-input"
                               min="1" value="1" required>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small">مرفق (صورة / صوت / فيديو) — اختياري</label>
                    <input type="file" name="attachment" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-sm" id="add-question-btn">إضافة السؤال</button>
            </form>
        </div>
    </div>
</div>

<style>
    .correct-checkbox-wrap {
        border: 2px solid #ced4da;
        border-radius: 8px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        transition: border-color .15s ease;
    }
    .correct-checkbox-wrap:has(input:checked) {
        border-color: #198754;
        background: #f0fff4;
    }
    .correct-checkbox-wrap input {
        width: 18px;
        height: 18px;
    }
    .points-input {
        border: 2px solid #0d6efd;
        border-radius: 8px;
        font-weight: bold;
        text-align: center;
        background: #fff;
    }
    .points-input:focus {
        border-color: #0a58ca;
        box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .25);
    }

    /* ---- كارت السؤال (تصميم B) ---- */
    .exam-question-card {
        display: flex;
        background: #fff;
        border: 1px solid #e3e7f2;
        border-radius: 12px;
        overflow: hidden;
    }
    .eqc-stripe {
        width: 5px;
        flex-shrink: 0;
        background: var(--red-600, #d81f26);
    }
    .eqc-body {
        padding: 16px;
        flex: 1;
        min-width: 0;
    }
    .q-number-badge {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--navy-900, #0b1f45);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .btn-icon-danger, .btn-icon-muted {
        border: 1px solid #e3e7f2;
        background: transparent;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }
    .btn-icon-danger { color: var(--red-600, #d81f26); }
    .btn-icon-danger:hover { background: #fdecec; }
    .btn-icon-muted { color: #8fa0c7; border-color: transparent; }
    .btn-icon-muted:hover { color: var(--red-600, #d81f26); }
    .btn-icon-danger svg, .btn-icon-muted svg { width: 15px; height: 15px; }

    .choices-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .choice-chip {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #e3e7f2;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        background: #fff;
    }
    .choice-chip.correct {
        border-color: #639922;
        background: #eaf3de;
        color: #27500a;
    }
    .correct-mark svg { width: 15px; height: 15px; color: #3b6d11; }

    .btn-add-choice {
        background: var(--navy-700, #1e4483);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 500;
    }
    .btn-add-choice:hover { background: var(--navy-800, #15356e); }
</style>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const APP_URL = "{{ rtrim(url('/'), '/') }}";
    const examId = {{ $exam->id }};
    const questionsList = document.getElementById('questions-list');
    const questionsCount = document.getElementById('questions-count');
    const flashBox = document.getElementById('flash-box');

    function flash(msg) {
        flashBox.textContent = msg;
        flashBox.classList.remove('d-none');
        setTimeout(() => flashBox.classList.add('d-none'), 2500);
    }

    function fetchJson(url, options = {}) {
        return fetch(APP_URL + url, {
            ...options,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                ...(options.headers || {}),
            },
        }).then(res => res.json().then(data => ({ ok: res.ok, data })));
    }

    function attachmentHtml(question) {
        if (!question.attachment_path) return '';
        if (question.attachment_type === 'image') {
            return `<div class="my-2"><img src="${question.attachment_url}" style="max-height:150px;" class="rounded"></div>`;
        }
        if (question.attachment_type === 'audio') {
            return `<div class="my-2"><audio controls src="${question.attachment_url}"></audio></div>`;
        }
        if (question.attachment_type === 'video') {
            return `<div class="my-2"><video controls src="${question.attachment_url}" style="max-height:150px;"></video></div>`;
        }
        return '';
    }

    function choiceHtml(choice) {
        return `
            <div class="choice-chip ${choice.is_correct ? 'correct' : ''}" data-choice-id="${choice.id}">
                <span class="choice-chip-text">${choice.choice_text}</span>
                <span class="d-flex align-items-center gap-2">
                    <span class="correct-mark">${choice.is_correct ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>' : ''}</span>
                    <button type="button" class="btn-icon-muted" onclick="deleteChoiceHandler(${choice.id}, this)" title="حذف">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                    </button>
                </span>
            </div>`;
    }

    function questionCardHtml(question, index) {
        const choicesHtml = (question.choices || []).map(choiceHtml).join('');

        return `
        <div class="exam-question-card mb-3" data-question-id="${question.id}">
            <div class="eqc-stripe"></div>
            <div class="eqc-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="q-number-badge">${index + 1}</span>
                        <div class="fw-bold question-text">${question.question_text}</div>
                        <span class="badge bg-light text-dark">${question.points} درجة</span>
                    </div>
                    <button type="button" class="btn-icon-danger" onclick="deleteQuestionHandler(${question.id}, this)" title="حذف السؤال">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    </button>
                </div>

                ${attachmentHtml(question)}

                <div class="choices-list">
                    ${choicesHtml}
                </div>

                <div class="add-choice-error alert alert-danger d-none mt-2"></div>

                <form class="add-choice-form d-flex gap-2 mt-3" data-question-id="${question.id}">
                    <input type="text" name="choice_text" class="form-control form-control-sm" placeholder="نص اختيار جديد" required>
                    <div class="correct-checkbox-wrap">
                        <input type="checkbox" name="is_correct" value="1" id="correct_new_${question.id}">
                        <label class="small mb-0" for="correct_new_${question.id}">صحيح</label>
                    </div>
                    <button type="submit" class="btn-add-choice text-nowrap">+ إضافة اختيار</button>
                </form>
            </div>
        </div>`;
    }

    // ---- إضافة سؤال جديد ----
    const addQuestionForm = document.getElementById('add-question-form');
    const addQuestionError = document.getElementById('add-question-error');
    const addQuestionBtn = document.getElementById('add-question-btn');

    addQuestionForm.addEventListener('submit', function (e) {
        e.preventDefault();
        addQuestionError.classList.add('d-none');
        addQuestionBtn.disabled = true;

        const formData = new FormData(addQuestionForm);

        fetchJson(`/exams/${examId}/questions`, { method: 'POST', body: formData })
            .then(({ ok, data }) => {
                addQuestionBtn.disabled = false;

                if (!ok) {
                    const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'حصل خطأ');
                    addQuestionError.textContent = firstError;
                    addQuestionError.classList.remove('d-none');
                    return;
                }

                const index = questionsList.querySelectorAll('[data-question-id]').length;
                questionsList.insertAdjacentHTML('beforeend', questionCardHtml(data.question, index));
                questionsCount.textContent = index + 1;
                addQuestionForm.reset();
                flash('تم إضافة السؤال');
            });
    });

    // ---- تفويض الأحداث لكل حاجة جوا questions-list (سؤال موجود أو مضاف حديثًا) ----
    questionsList.addEventListener('submit', function (e) {
        if (!e.target.classList.contains('add-choice-form')) return;
        e.preventDefault();

        const form = e.target;
        const questionId = form.dataset.questionId;
        const errorBox = form.closest('.eqc-body').querySelector('.add-choice-error');
        errorBox.classList.add('d-none');

        const formData = new FormData(form);
        const body = new URLSearchParams();
        body.append('choice_text', formData.get('choice_text'));
        if (formData.get('is_correct')) body.append('is_correct', '1');

        fetchJson(`/exam-questions/${questionId}/choices`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        }).then(({ ok, data }) => {
            if (!ok) {
                const firstError = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'حصل خطأ');
                errorBox.textContent = firstError;
                errorBox.classList.remove('d-none');
                return;
            }

            const choicesList = form.closest('.eqc-body').querySelector('.choices-list');

            if (data.unset_others) {
                choicesList.querySelectorAll('.choice-chip.correct').forEach(el => {
                    el.classList.remove('correct');
                    el.querySelector('.correct-mark').innerHTML = '';
                });
            }

            choicesList.insertAdjacentHTML('beforeend', choiceHtml(data.choice));
            form.reset();
            flash('تم إضافة الاختيار');
        });
    });

    // ---- حذف سؤال (مربوطة مباشرة من onclick على كل زرار) ----
    window.deleteQuestionHandler = function (questionId, btnEl) {
        if (!confirm('حذف السؤال؟')) return;

        const card = btnEl.closest('[data-question-id]');
        btnEl.disabled = true;

        fetchJson(`/exam-questions/${questionId}`, { method: 'DELETE' })
            .then(({ ok, data }) => {
                if (!ok) {
                    alert(data.message || 'حصل خطأ أثناء حذف السؤال');
                    btnEl.disabled = false;
                    return;
                }
                card.remove();
                questionsCount.textContent = questionsList.querySelectorAll('[data-question-id]').length;
                flash('تم حذف السؤال');
            })
            .catch((err) => {
                console.error('deleteQuestionHandler error:', err);
                alert('حصل خطأ في الاتصال أثناء حذف السؤال، افتح الـ Console وابعتلي التفاصيل.');
                btnEl.disabled = false;
            });
    };

    // ---- حذف اختيار (مربوطة مباشرة من onclick على كل زرار) ----
    window.deleteChoiceHandler = function (choiceId, btnEl) {
        if (!confirm('حذف الاختيار؟')) return;

        const li = btnEl.closest('[data-choice-id]');
        btnEl.disabled = true;

        fetchJson(`/exam-choices/${choiceId}`, { method: 'DELETE' })
            .then(({ ok, data }) => {
                if (!ok) {
                    alert(data.message || 'حصل خطأ أثناء حذف الاختيار');
                    btnEl.disabled = false;
                    return;
                }
                li.remove();
                flash('تم حذف الاختيار');
            })
            .catch((err) => {
                console.error('deleteChoiceHandler error:', err);
                alert('حصل خطأ في الاتصال أثناء حذف الاختيار، افتح الـ Console وابعتلي التفاصيل.');
                btnEl.disabled = false;
            });
    };

    // ---- الذهاب لصفحة التفعيل ----
    const goBtn = document.getElementById('go-to-manage-btn');
    if (goBtn) {
        goBtn.addEventListener('click', function () {
            const groupId = document.getElementById('group-select').value;
            window.location.href = `${APP_URL}/exams/${examId}/groups/${groupId}/sessions`;
        });
    }
})();
</script>
@endsection
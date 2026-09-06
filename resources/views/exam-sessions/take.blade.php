@extends('layouts.app')

@section('title', $examSession->exam->title)

@section('content')

<style>
    .watermark-edge-strip {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 30px;
        z-index: 9999;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(11, 31, 69, 0.06);
        overflow: hidden;
    }
    .watermark-edge-strip span {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        color: rgba(11, 31, 69, 0.35);
        user-select: none;
    }
</style>

<div class="watermark-edge-strip">
    <span>{{ $examSession->student->name }}</span>
</div>

<div class="container" dir="rtl" style="max-width: 720px; margin-left: 40px;">
    <h4 class="mb-1">{{ $examSession->exam->title }}</h4>
    <p class="text-muted mb-4">جاوب على كل الأسئلة ثم اضغط "تسليم الامتحان" في الآخر.</p>

    <div id="error-box" class="alert alert-danger d-none"></div>

    <div id="timer-box" class="alert alert-secondary d-none d-flex justify-content-between align-items-center">
        <span>الوقت المتبقي</span>
        <strong id="timer-value" class="fs-5">--:--</strong>
    </div>

    <form id="exam-form">
        @foreach ($questions as $index => $question)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="fw-bold mb-2">
                        {{ $index + 1 }}. {{ $question->question_text }}
                        <span class="badge bg-light text-dark">{{ $question->points }} درجة</span>
                    </div>

                    @if ($question->attachment_path)
                        <div class="mb-3">
                            @if ($question->attachment_type === 'image')
                                <img src="{{ $question->attachment_url }}" class="img-fluid rounded" style="max-height:300px;">
                            @elseif ($question->attachment_type === 'audio')
                                <audio controls src="{{ $question->attachment_url }}" class="w-100"></audio>
                            @elseif ($question->attachment_type === 'video')
                                <video controls src="{{ $question->attachment_url }}" class="w-100" style="max-height:300px;"></video>
                            @endif
                        </div>
                    @endif

                    @foreach ($question->orderedChoices as $choice)
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="q_{{ $question->id }}" value="{{ $choice->id }}"
                                   id="choice_{{ $choice->id }}">
                            <label class="form-check-label" for="choice_{{ $choice->id }}">
                                {{ $choice->choice_text }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success w-100 mb-4" id="submit-btn">تسليم الامتحان</button>
    </form>
</div>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const APP_URL = "{{ rtrim(url('/'), '/') }}";
    const sessionId = {{ $examSession->id }};
    const isPlacement = @json($examSession->exam->type === 'placement');
    const questionIds = @json($questions->pluck('id'));
    const form = document.getElementById('exam-form');
    const errorBox = document.getElementById('error-box');
    const submitBtn = document.getElementById('submit-btn');

    let remainingSeconds = @json($remainingSeconds ?? null);
    let timerInterval = null;
    let alreadySubmitting = false;

    function collectAnswers() {
        const answers = {};
        questionIds.forEach(qId => {
            const selected = form.querySelector(`input[name="q_${qId}"]:checked`);
            answers[qId] = selected ? selected.value : null;
        });
        return answers;
    }

    function submitAnswers(isAuto) {
        if (alreadySubmitting) return;
        alreadySubmitting = true;

        if (timerInterval) clearInterval(timerInterval);

        errorBox.classList.add('d-none');
        submitBtn.disabled = true;

        fetch(`${APP_URL}/exam-sessions/${sessionId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ answers: collectAnswers() }),
        })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    errorBox.textContent = data.message || 'حصل خطأ أثناء التسليم';
                    errorBox.classList.remove('d-none');
                    submitBtn.disabled = false;
                    alreadySubmitting = false;
                    return;
                }

                if (isAuto) {
                    if (isPlacement) {
                        alert('انتهى وقت الامتحان، وتم تسليمه تلقائيًا.');
                    } else {
                        alert('انتهى وقت الامتحان، تم تسليمه تلقائيًا. درجتك: ' + data.score + ' / ' + data.total_points);
                    }
                } else {
                    if (isPlacement) {
                        alert('تم تسليم الامتحان بنجاح.');
                    } else {
                        alert(`تم التسليم! درجتك: ${data.score} / ${data.total_points}`);
                    }
                }
                window.location.href = "{{ route('student.exam-sessions.index') }}";
            })
            .catch(() => {
                errorBox.textContent = 'حصل خطأ في الاتصال، حاول تاني.';
                errorBox.classList.remove('d-none');
                submitBtn.disabled = false;
                alreadySubmitting = false;
            });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!confirm('متأكد إنك عايز تسلّم الامتحان؟ مش هتقدر تعدّل بعد كده.')) return;

        submitAnswers(false);
    });

    // ---- العداد التنازلي (لو الامتحان له حد وقت) ----
    if (remainingSeconds !== null) {
        const timerBox = document.getElementById('timer-box');
        const timerValue = document.getElementById('timer-value');
        timerBox.classList.remove('d-none');

        function formatTime(totalSeconds) {
            const m = Math.floor(totalSeconds / 60);
            const s = totalSeconds % 60;
            return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }

        function tick() {
            timerValue.textContent = formatTime(remainingSeconds);

            if (remainingSeconds <= 60) {
                timerBox.classList.remove('alert-secondary');
                timerBox.classList.add('alert-danger');
            }

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                submitAnswers(true);
                return;
            }

            remainingSeconds -= 1;
        }

        tick();
        timerInterval = setInterval(tick, 1000);
    }

    // ---- فحص دوري: هل الأدمن أنهى الجلسة يدويًا وإحنا لسه فاتحين الصفحة؟ ----
    var statusCheckInterval = setInterval(function () {
        if (alreadySubmitting) return;

        fetch(APP_URL + '/exam-sessions/' + sessionId + '/status', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.status !== 'active') {
                clearInterval(statusCheckInterval);
                if (timerInterval) clearInterval(timerInterval);
                alreadySubmitting = true;

                alert('تم إنهاء الجلسة من قِبل الأدمن. مش هتقدر تكمل الامتحان.');
                window.location.href = "{{ route('student.exam-sessions.index') }}";
            }
        })
        .catch(function () {});
    }, 10000);
})();
</script>
@endsection
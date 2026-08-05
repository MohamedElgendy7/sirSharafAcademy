@extends('layouts.app')

@section('title', $examSession->exam->title)

@section('content')
<div class="container" dir="rtl" style="max-width: 720px;">
    <h4 class="mb-1">{{ $examSession->exam->title }}</h4>
    <p class="text-muted mb-4">جاوب على كل الأسئلة ثم اضغط "تسليم الامتحان" في الآخر.</p>

    <div id="error-box" class="alert alert-danger d-none"></div>

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
    const questionIds = @json($questions->pluck('id'));
    const form = document.getElementById('exam-form');
    const errorBox = document.getElementById('error-box');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!confirm('متأكد إنك عايز تسلّم الامتحان؟ مش هتقدر تعدّل بعد كده.')) return;

        errorBox.classList.add('d-none');
        submitBtn.disabled = true;

        const answers = {};
        questionIds.forEach(qId => {
            const selected = form.querySelector(`input[name="q_${qId}"]:checked`);
            answers[qId] = selected ? selected.value : null;
        });

        fetch(`${APP_URL}/exam-sessions/${sessionId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ answers }),
        })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    errorBox.textContent = data.message || 'حصل خطأ أثناء التسليم';
                    errorBox.classList.remove('d-none');
                    submitBtn.disabled = false;
                    return;
                }

                alert(`تم التسليم! درجتك: ${data.score} / ${data.total_points}`);
                window.location.href = "{{ route('student.exam-sessions.index') }}";
            })
            .catch(() => {
                errorBox.textContent = 'حصل خطأ في الاتصال، حاول تاني.';
                errorBox.classList.remove('d-none');
                submitBtn.disabled = false;
            });
    });
})();
</script>
@endsection

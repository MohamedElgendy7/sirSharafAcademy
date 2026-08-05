@extends('layouts.app')

@section('title', 'إدخال كود الامتحان')

@section('content')
<div class="container" dir="rtl" style="max-width: 420px;">
    <h4 class="mb-3">{{ $examSession->exam->title }}</h4>
    <p class="text-muted">ادخل الكود المكوّن من 8 أرقام الظاهر على شاشة المدرّس.</p>

    <div id="error-box" class="alert alert-danger d-none"></div>

    <form id="code-form">
        <div class="mb-3">
            <input type="text" id="code-input" class="form-control form-control-lg text-center"
                   maxlength="8" inputmode="numeric" placeholder="········" autofocus required>
        </div>
        <button type="submit" class="btn btn-primary w-100" id="submit-btn">دخول</button>
    </form>
</div>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const APP_URL = "{{ rtrim(url('/'), '/') }}";
    const sessionId = {{ $examSession->id }};
    const form = document.getElementById('code-form');
    const errorBox = document.getElementById('error-box');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errorBox.classList.add('d-none');
        submitBtn.disabled = true;

        const code = document.getElementById('code-input').value.trim();

        fetch(`${APP_URL}/exam-sessions/${sessionId}/verify-code`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ code }),
        })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    errorBox.textContent = data.message || 'الكود غير صحيح';
                    errorBox.classList.remove('d-none');
                    submitBtn.disabled = false;
                    return;
                }

                window.location.href = `${APP_URL}/exam-sessions/${sessionId}/take`;
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

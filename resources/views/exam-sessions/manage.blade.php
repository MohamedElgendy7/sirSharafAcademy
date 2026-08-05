@extends('layouts.app')

@section('title', 'تفعيل الامتحان — ' . $exam->title)

@section('content')

<style>
  .activate-page{
    box-sizing:border-box;
  }

  .activate-page .page-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
    flex-wrap:wrap;
    gap:12px;
  }
  .activate-page .page-head h4{
    font-weight:800;
    color:var(--ink-900, #0e1930);
    margin-bottom:2px;
  }
  .activate-page .page-head small{
    color:var(--ink-500, #5b6b8c);
  }

  /* زرار "تفعيل الامتحان لكل الجروب" - أحمر بتاعنا */
  #activate-all-btn{
    background:var(--red-600, #d81f26);
    border:none;
    color:#fff;
    font-weight:700;
    padding:9px 18px;
    border-radius:10px;
    transition:opacity .15s linear;
  }
  #activate-all-btn:hover{
    opacity:.85;
    color:#fff;
  }
  #activate-all-btn:disabled{
    opacity:.6;
  }

  /* الجدول - حواف مدورة من برة بس + بوردر أسود واضح لكل خلية */
  .table-wrapper{
    border:1px solid #000;
    border-radius:12px;
    overflow:hidden;
  }

  #students-table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:0;
  }

  #students-table thead th{
    background:var(--navy-900, #0b1f45);
    color:#fff;
    font-weight:700;
    padding:14px 20px;
    text-align:center;
    border:1px solid #000;
  }

  #students-table tbody td{
    border:1px solid #000;
    padding:14px 20px;
    text-align:center;
    vertical-align:middle;
    line-height:1.6;
    color:var(--ink-900, #0e1930);
  }

  #students-table tbody tr:nth-of-type(odd){
    background-color: rgba(11, 31, 69, 0.04);
  }

  /* شارات الحالة */
  .status-badge{
    display:inline-block;
    font-size:12px;
    font-weight:700;
    padding:5px 12px;
    border-radius:20px;
    color:#fff;
  }
  .status-badge.status-none{
    background:#6c757d;
  }
  .status-badge.status-active{
    background:var(--red-600, #d81f26);
  }
  .status-badge.status-ended{
    background:var(--navy-900, #0b1f45);
  }

  /* زرار تفعيل فردي - أحمر */
  .activate-btn{
    background:var(--red-600, #d81f26);
    border:none;
    color:#fff;
    font-weight:700;
    font-size:12.5px;
    padding:6px 14px;
    border-radius:8px;
    transition:opacity .15s linear;
  }
  .activate-btn:hover{
    opacity:.85;
    color:#fff;
  }
  .activate-btn:disabled{
    opacity:.6;
  }

  /* زرار إنهاء الجلسة - كحلي مملوء بدل outline */
  .end-btn{
    background:var(--navy-900, #0b1f45);
    border:none;
    color:#fff;
    font-weight:700;
    font-size:12.5px;
    padding:6px 14px;
    border-radius:8px;
    transition:opacity .15s linear;
  }
  .end-btn:hover{
    opacity:.85;
    color:#fff;
  }

  .code-value{
    color:var(--ink-900, #0e1930);
  }
</style>

<div class="activate-page container-fluid" dir="rtl">

    <div class="page-head">
        <div>
            <h4>{{ $exam->title }}</h4>
            <small>جروب: {{ $group->name }} — مستوى {{ $exam->level }}</small>
        </div>

        <button id="activate-all-btn">
            تفعيل الامتحان لكل الجروب
        </button>
    </div>

    @if ($students->isEmpty())
        <div class="alert alert-warning">مفيش طلاب نشطين في الجروب ده حاليًا.</div>
    @endif

    <div class="table-wrapper">
        <table id="students-table">
            <thead>
                <tr>
                    <th>الطالب</th>
                    <th>الحالة</th>
                    <th>الكود الحالي</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    @php $session = $sessions->get($student->id); @endphp
                    <tr id="row-student-{{ $student->id }}" data-student-id="{{ $student->id }}">
                        <td>{{ $student->name }}</td>

                        <td class="status-cell">
                            @if (! $session)
                                <span class="status-badge status-none">لسه متفعّلش</span>
                            @elseif ($session->status === 'active')
                                <span class="status-badge status-active">نشط</span>
                            @else
                                <span class="status-badge status-ended">منتهي</span>
                            @endif
                        </td>

                        <td class="code-cell" data-session-id="{{ $session->id ?? '' }}">
                            @if ($session && $session->status === 'active')
                                <span class="code-value fs-5 fw-bold">------</span>
                                <div class="small text-muted">بيتجدد كل دقيقة</div>
                            @else
                                —
                            @endif
                        </td>

                        <td class="action-cell">
                            @if (! $session || $session->status === 'ended')
                                <button class="activate-btn"
                                        data-student-id="{{ $student->id }}">
                                    تفعيل
                                </button>
                            @else
                                <button class="end-btn"
                                        data-session-id="{{ $session->id }}">
                                    إنهاء الجلسة
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const APP_URL = "{{ rtrim(url('/'), '/') }}";

    const examId = {{ $exam->id }};
    const groupId = {{ $group->id }};

    function postJson(url) {
        return fetch(APP_URL + url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        }).then(res => res.json().then(data => ({ ok: res.ok, data })));
    }

    // تفعيل فردي
    document.querySelectorAll('.activate-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const studentId = this.dataset.studentId;
            this.disabled = true;

            postJson(`/exams/${examId}/groups/${groupId}/students/${studentId}/activate`)
                .then(({ ok, data }) => {
                    if (!ok) {
                        alert(data.message || 'حصل خطأ');
                        this.disabled = false;
                        return;
                    }
                    location.reload();
                });
        });
    });

    // تفعيل جماعي
    document.getElementById('activate-all-btn').addEventListener('click', function () {
        this.disabled = true;

        postJson(`/exams/${examId}/groups/${groupId}/activate-all`)
            .then(({ ok, data }) => {
                if (!ok) {
                    alert(data.message || 'حصل خطأ');
                    this.disabled = false;
                    return;
                }
                alert(`تم تفعيل ${data.activated_count} طالب، وتخطي ${data.skipped_count} كانوا مفعّلين بالفعل`);
                location.reload();
            });
    });

    // إنهاء الجلسة
    document.querySelectorAll('.end-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const sessionId = this.dataset.sessionId;
            if (!confirm('متأكد إنك عايز تنهي جلسة الطالب ده؟')) return;

            postJson(`/exam-sessions/${sessionId}/end`)
                .then(({ ok, data }) => {
                    if (!ok) {
                        alert(data.message || 'حصل خطأ');
                        return;
                    }
                    location.reload();
                });
        });
    });

    // تحديث الكود لكل الجلسات النشطة كل 5 ثواني (lazy generation بيحصل من السيرفر نفسه)
    function refreshCodes() {
        document.querySelectorAll('.code-cell[data-session-id]').forEach(cell => {
            const sessionId = cell.dataset.sessionId;
            if (!sessionId) return;

            fetch(`${APP_URL}/exam-sessions/${sessionId}/code`, {
                headers: { 'Accept': 'application/json' },
            })
                .then(res => res.json())
                .then(data => {
                    const codeEl = cell.querySelector('.code-value');
                    if (codeEl && data.code) {
                        codeEl.textContent = data.code;
                    }
                })
                .catch(() => {});
        });
    }

    refreshCodes();
    setInterval(refreshCodes, 5000);
})();
</script>
@endsection

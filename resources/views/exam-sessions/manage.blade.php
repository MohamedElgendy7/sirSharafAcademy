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

  /* بانل إعدادات التفعيل (النسخة + الوقت) */
  .activation-settings{
    background:#fff;
    border:1px solid var(--eqc-border, rgba(30,68,131,.3));
    border-radius:12px;
    padding:16px 18px;
    margin-bottom:18px;
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    align-items:flex-end;
  }
  .activation-settings .field{
    display:flex;
    flex-direction:column;
    gap:6px;
  }
  .activation-settings label{
    font-size:12.5px;
    font-weight:700;
    color:var(--ink-900, #0e1930);
  }
  .activation-settings .hint{
    font-size:11px;
    color:var(--ink-500, #5b6b8c);
    font-weight:400;
  }
  .activation-settings select,
  .activation-settings input[type="number"]{
    border:1.5px solid #000;
    border-radius:8px;
    padding:8px 12px;
    font-family:inherit;
    font-size:13px;
    min-width:220px;
  }
  .activation-settings input[type="number"]{
    min-width:140px;
  }

  /* الجدول - حواف مدورة من برة بس + بوردر أسود واضح لكل خلية */
  .table-wrapper{
    border:1px solid #000;
    border-radius:12px;
    overflow:hidden;
    overflow-x:auto;
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
    white-space:nowrap;
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

  .version-badge{
    display:inline-block;
    font-size:11px;
    font-weight:700;
    padding:3px 10px;
    border-radius:14px;
    background:#fff3cd;
    color:#8a6d00;
  }

  .time-badge{
    font-size:12px;
    color:var(--ink-500, #5b6b8c);
  }
  .time-badge.no-limit{
    color:#8fa0c7;
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
            <small> جروب :  {{ $group->name }} <br> كورس  : {{ App\Models\course::find($exam->course_id)->name ?? '—' }}<br> مستوى : {{ App\Models\level::find($exam->level_id)->name ?? '—' }}</small>
        </div>

        <button id="activate-all-btn">
            تفعيل الامتحان لكل الجروب
        </button>
    </div>

    {{-- إعدادات التفعيل: النسخة (لو الامتحان تابع لمجموعة نسخ) + الوقت المحدد --}}
    <div class="activation-settings">
        @if ($exam->version_group_id)
            <div class="field">
                <label>نسخة الامتحان</label>
                <select id="version-select">
                    <option value="0">استخدام هذه النسخة تحديدًا ({{ $exam->title }})</option>
                    <option value="1">نسخة عشوائية من نفس المجموعة</option>
                </select>
                <span class="hint">لو اخترت "عشوائية"، السيستم هيختار نسخة مختلفة لكل طالب تلقائيًا ويسجلها في تقريره.</span>
            </div>
        @endif

        <div class="field">
            <label>حد أقصى للوقت (بالدقايق)</label>
            <input type="number" id="time-limit-input" min="1" placeholder="بدون حد وقت">
            <span class="hint">سيبها فاضية لو عايز الامتحان يفضل مفتوح من غير وقت محدد.</span>
        </div>
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
                    @if ($exam->version_group_id)
                        <th>النسخة</th>
                    @endif
                    <th>الوقت</th>
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

                        @if ($exam->version_group_id)
                            <td>
                                @if ($session)
                                    {{ $session->exam->title ?? '—' }}
                                    @if ($session->is_random_version)
                                        <br><span class="version-badge">عشوائية</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                        @endif

                        <td>
                            @if ($session && $session->time_limit_minutes)
                                <span class="time-badge">{{ $session->time_limit_minutes }} دقيقة</span>
                            @elseif ($session)
                                <span class="time-badge no-limit">بدون حد</span>
                            @else
                                —
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

    const versionSelect = document.getElementById('version-select');
    const timeLimitInput = document.getElementById('time-limit-input');

    function currentSettings() {
        return {
            random_version: versionSelect ? versionSelect.value === '1' : false,
            time_limit_minutes: timeLimitInput.value ? parseInt(timeLimitInput.value, 10) : null,
        };
    }

    function postJson(url, body = {}) {
        return fetch(APP_URL + url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        }).then(res => res.json().then(data => ({ ok: res.ok, data })));
    }

    // تفعيل فردي
    document.querySelectorAll('.activate-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const studentId = this.dataset.studentId;
            this.disabled = true;

            postJson(`/exams/${examId}/groups/${groupId}/students/${studentId}/activate`, currentSettings())
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

        postJson(`/exams/${examId}/groups/${groupId}/activate-all`, currentSettings())
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
@extends('layouts.app')

@section('content')

<style>
  .student-page{
    box-sizing:border-box;
  }

  .student-page .page-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    margin-bottom:18px;
    flex-wrap:wrap;
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
  .page-head .actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
  }

  .alert-success{
    background:#eafbf1;
    color:#1a9c5c;
    border:1px solid #b9ecd0;
    border-radius:10px;
    padding:10px 16px;
    font-size:13px;
    font-weight:600;
    margin-bottom:14px;
  }

  .panel{
    background:var(--paper-card, #fff);
    border:1px solid var(--border, #e3e7f2);
    border-radius:var(--radius, 14px);
    padding:clamp(16px, 2.5vw, 28px);
    margin-bottom:18px;
  }

  /* ---- ملخص سريع ---- */
  .summary-cards{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));
    gap:14px;
    margin-bottom:18px;
  }
  .summary-card{
    background:var(--paper-card, #fff);
    border:1px solid var(--border, #e3e7f2);
    border-radius:var(--radius, 14px);
    padding:16px 18px;
    text-align:center;
  }
  .summary-card .value{
    font-size:24px;
    font-weight:800;
    color:var(--navy-900, #0b1f45);
  }
  .summary-card .label{
    font-size:12px;
    color:var(--ink-500, #5b6b8c);
    margin-top:4px;
  }

  /* ---- بيانات أساسية ---- */
  .profile-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));
    gap:16px;
  }
  .profile-field .lbl{
    font-size:12.5px;
    font-weight:700;
    color:var(--ink-900, #0e1930);
    margin-bottom:6px;
  }
  .profile-field .val{
    font-size:14px;
    font-weight:700;
    color:var(--ink-900, #0e1930);
    padding:10px 14px;
    border:1.5px solid #000;
    border-radius:10px;
    background:#fff;
  }

  /* ---- الجروب الحالي ---- */
  .current-group-box{
    display:flex;
    justify-content:space-between;
    align-items:center;
    border:1.5px solid #000;
    border-radius:10px;
    padding:12px 16px;
  }
  .current-group-box .empty{
    color:var(--ink-500, #5b6b8c);
    font-size:13.5px;
  }

  /* ---- امتحان تحديد المستوى (مميز) ---- */
  .placement-card{
    border:2px solid var(--red-600, #d81f26);
    border-radius:12px;
    padding:16px 18px;
    background:#fff8f8;
    margin-bottom:10px;
  }
  .placement-card .title{
    font-weight:800;
    color:var(--red-700, #b5171d);
    margin-bottom:6px;
  }
  .placement-card .score{
    font-size:20px;
    font-weight:800;
    color:var(--navy-900, #0b1f45);
  }
  .placement-card .meta{
    font-size:12px;
    color:var(--ink-500, #5b6b8c);
    margin-top:4px;
  }

  /* ---- جدول عام ---- */
  .data-table-wrapper{
    border:1px solid #000;
    border-radius:12px;
    overflow:hidden;
    overflow-x:auto;
  }
  .data-table{
    width:100%;
    border-collapse:collapse;
    font-size:13.5px;
  }
  .data-table thead th{
    background:var(--navy-900, #0b1f45);
    color:#fff;
    font-weight:700;
    padding:12px 16px;
    text-align:center;
    white-space:nowrap;
  }
  .data-table td{
    border:1px solid #000;
    padding:12px 16px;
    text-align:center;
    vertical-align:middle;
  }
  .data-table tbody tr:nth-of-type(odd){
    background-color: rgba(11, 31, 69, 0.03);
  }

  .badge-random{
    display:inline-block;
    font-size:10.5px;
    font-weight:700;
    padding:2px 9px;
    border-radius:14px;
    background:#fff3cd;
    color:#8a6d00;
  }

  .status-pill{
    display:inline-block;
    font-size:11.5px;
    font-weight:700;
    padding:3px 11px;
    border-radius:16px;
    color:#fff;
  }
  .status-pill.present{ background:#1a9c5c; }
  .status-pill.absent{ background:var(--red-600, #d81f26); }
  .status-pill.late{ background:#c98a13; }

  .empty-state{
    color:var(--ink-500, #5b6b8c);
    font-size:13.5px;
    padding:20px;
    text-align:center;
  }

  /* ---- زرار إظهار/إخفاء المحتوى (لكل أقسام الصفحة) ---- */
  details.section-toggle summary{
    list-style:none;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    background:var(--navy-900, #0b1f45);
    color:#fff;
    font-weight:700;
    font-size:14px;
    padding:12px 18px;
    border-radius:10px;
  }
  details.section-toggle summary::-webkit-details-marker{ display:none; }
  details.section-toggle summary::after{
    content:'▾';
    font-size:12px;
    transition:transform .15s ease;
  }
  details.section-toggle[open] summary{ border-radius:10px 10px 0 0; background:var(--navy-800, #15356e); }
  details.section-toggle[open] summary::after{ transform:rotate(180deg); }
  details.section-toggle .section-body{
    padding-top:16px;
  }

  /* ---- تقييمات ---- */
  .evaluation-card{
    border:1px solid var(--border, #e3e7f2);
    border-radius:10px;
    padding:14px 16px;
    margin-bottom:10px;
  }
  .evaluation-card .top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:6px;
  }
  .evaluation-card .skill{
    font-weight:800;
    color:var(--navy-900, #0b1f45);
  }
  .evaluation-card .level-tag{
    font-size:11px;
    background:#f2f4f9;
    color:#5b6b8c;
    padding:2px 10px;
    border-radius:14px;
  }
  .evaluation-card .comment{
    font-size:13px;
    color:var(--ink-900, #0e1930);
  }
  .evaluation-card .meta{
    font-size:11px;
    color:var(--ink-500, #5b6b8c);
    margin-top:6px;
  }
</style>

<div class="student-page">

  <div class="page-head">
    <div>
      <h1>{{ $student->name }}</h1>
      <p>بروفايل الطالب الشامل — الامتحانات، الحضور، والتقييمات</p>
    </div>
    <div class="actions">
      <a href="{{ route('students.edit', $student) }}" class="btn btn-ghost">تعديل البيانات</a>
      <a href="{{ route('students.index') }}" class="btn btn-ghost">رجوع للقائمة</a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  {{-- ============ 1) ملخص سريع (ظاهر دايمًا) ============ --}}
  <div class="summary-cards">
    <div class="summary-card">
      <div class="value">{{ $examsCount }}</div>
      <div class="label">عدد الامتحانات المحلولة</div>
    </div>
    <div class="summary-card">
      <div class="value">{{ $averageScorePercent !== null ? $averageScorePercent . '%' : '—' }}</div>
      <div class="label">متوسط الدرجات</div>
    </div>
    <div class="summary-card">
      <div class="value">{{ $attendancePercent !== null ? $attendancePercent . '%' : '—' }}</div>
      <div class="label">نسبة الحضور</div>
    </div>
  </div>

  {{-- ============ البيانات الأساسية ============ --}}
  <div class="panel">
    <details class="section-toggle">
      <summary>البيانات الأساسية</summary>
      <div class="section-body">
        <div class="profile-grid">
          <div class="profile-field">
            <div class="lbl">رقم الهاتف</div>
            <div class="val">{{ $student->phone }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">رقم الواتساب</div>
            <div class="val">{{ $student->whatsapp ?? '—' }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">البريد الإلكتروني</div>
            <div class="val">{{ $student->email ?? '—' }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">السن</div>
            <div class="val">{{ $student->age ?? '—' }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">النوع</div>
            <div class="val">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">رقم ولي الأمر</div>
            <div class="val">{{ $student->guardian_phone ?? '—' }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">الفرع</div>
            <div class="val">{{ $student->branch_label ?? '—' }}</div>
          </div>
          <div class="profile-field">
            <div class="lbl">الكورس</div>
            <div class="val">{{ $student->course ?? '—' }}</div>
          </div>
        </div>
      </div>
    </details>
  </div>

  {{-- ============ الجروب الحالي ============ --}}
  <div class="panel">
    <details class="section-toggle">
      <summary>الجروب الحالي</summary>
      <div class="section-body">
        @if ($currentGroup)
          <div class="current-group-box">
            <div>
              <strong>{{ $currentGroup->name }}</strong>
              <span style="color:var(--ink-500,#5b6b8c); font-size:12.5px;"> — {{ $currentGroup->course }}</span>
            </div>
            <a href="{{ route('groups.show', $currentGroup) }}" class="btn btn-ghost btn-sm">فتح الجروب</a>
          </div>
        @else
          <div class="current-group-box">
            <span class="empty">الطالب مش مسجل في أي جروب نشط حاليًا او في انتظار تحديد المستوي</span>
          </div>
        @endif
      </div>
    </details>
  </div>

  {{-- ============ تفعيل / نتيجة امتحان تحديد المستوى ============ --}}
  <div class="panel">
    <details class="section-toggle">
      <summary>امتحان تحديد المستوى</summary>
      <div class="section-body">

        @if ($activePlacementSession)
          {{-- فيه جلسة شغالة بالفعل — نعرض الكود بدل فورم التفعيل --}}
          <div style="border:2px solid var(--red-600,#d81f26); border-radius:12px; padding:16px 18px; background:#fff8f8; margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
              <div>
                <div style="font-weight:800; color:var(--navy-900,#0b1f45); margin-bottom:4px;">
                  {{ $activePlacementSession->exam->title }}
                </div>
                <div style="font-size:12px; color:var(--ink-500,#5b6b8c);">
                  مفعّل من {{ $activePlacementSession->activated_at ? $activePlacementSession->activated_at->format('j/n/Y - h:i A') : '' }}
                  — الكود بيتجدد كل دقيقة تلقائي
                </div>
              </div>

              <div style="text-align:center;" class="code-cell" data-session-id="{{ $activePlacementSession->id }}">
                <span class="code-value" style="font-size:22px; font-weight:800; color:var(--navy-900,#0b1f45); letter-spacing:2px;">------</span>
              </div>

              <button id="end-placement-btn" data-session-id="{{ $activePlacementSession->id }}"
                      style="background:var(--navy-900,#0b1f45); color:#fff; border:none; padding:8px 16px; border-radius:8px; font-weight:700; font-size:12.5px;">
                إنهاء الجلسة
              </button>
            </div>
          </div>
        @elseif ($placementExams->isEmpty())
          <div class="empty-state">مفيش امتحانات تحديد مستوى متاحة في بنك الأسئلة حاليًا.</div>
        @else
          <div id="placement-error" class="alert alert-danger" style="display:none; margin-bottom:12px;"></div>

          <div style="display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; margin-bottom:16px;">
            <div style="display:flex; flex-direction:column; gap:6px;">
              <label style="font-size:12.5px; font-weight:700;">الامتحان</label>
              <select id="placement-exam-select" style="border:1.5px solid #000; border-radius:8px; padding:8px 12px; min-width:240px;">
                @foreach ($placementExams as $pExam)
                  <option value="{{ $pExam->id }}">{{ $pExam->title }}</option>
                @endforeach
              </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:6px;">
              <label style="font-size:12.5px; font-weight:700;">نوع النسخة</label>
              <select id="placement-version-select" style="border:1.5px solid #000; border-radius:8px; padding:8px 12px; min-width:200px;">
                <option value="0">النسخة المحددة</option>
                <option value="1">نسخة عشوائية (لو متاحة)</option>
              </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:6px;">
              <label style="font-size:12.5px; font-weight:700;">حد أقصى للوقت (بالدقايق)</label>
              <input type="number" id="placement-time-limit" min="1" placeholder="بدون حد وقت"
                     style="border:1.5px solid #000; border-radius:8px; padding:8px 12px; min-width:160px;">
            </div>

            <button id="activate-placement-btn" class="btn" style="background:var(--red-600,#d81f26); color:#fff; font-weight:700; padding:9px 20px; border:none; border-radius:10px;">
              تفعيل الامتحان
            </button>
          </div>
        @endif

        @if ($placementSubmissions->isEmpty())
          <div class="empty-state">الطالب لسه محلّش أي امتحان تحديد مستوى.</div>
        @else
          @foreach ($placementSubmissions as $submission)
            <div class="placement-card">
              <div class="title">{{ $submission->exam->title ?? 'امتحان تحديد مستوى' }}</div>
              <div class="score">{{ $submission->score }} / {{ $submission->total_points }}</div>
              <div class="meta">
                بتاريخ {{ $submission->submitted_at ? $submission->submitted_at->format('j/n/Y - h:i A') : '' }}
                @if ($submission->duration_seconds)
                  — استغرق {{ gmdate('i:s', $submission->duration_seconds) }} دقيقة
                @endif
              </div>
            </div>
          @endforeach
        @endif

      </div>
    </details>
  </div>

  <script>
  (function () {
      var btn = document.getElementById('activate-placement-btn');
      if (!btn) return;

      var csrfToken = document.querySelector('meta[name="csrf-token"]');
      csrfToken = csrfToken ? csrfToken.content : '';
      var APP_URL = "{{ rtrim(url('/'), '/') }}";
      var studentId = {{ $student->id }};
      var errorBox = document.getElementById('placement-error');

      btn.addEventListener('click', function () {
          var examId = document.getElementById('placement-exam-select').value;
          var randomVersion = document.getElementById('placement-version-select').value === '1';
          var timeLimitVal = document.getElementById('placement-time-limit').value;

          errorBox.style.display = 'none';
          btn.disabled = true;

          fetch(APP_URL + '/exams/' + examId + '/students/' + studentId + '/activate-placement', {
              method: 'POST',
              headers: {
                  'X-CSRF-TOKEN': csrfToken,
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                  random_version: randomVersion,
                  time_limit_minutes: timeLimitVal ? parseInt(timeLimitVal, 10) : null
              })
          })
          .then(function (res) {
              return res.json().then(function (data) { return { ok: res.ok, data: data }; });
          })
          .then(function (result) {
              if (!result.ok) {
                  errorBox.textContent = result.data.message || 'حصل خطأ';
                  errorBox.style.display = 'block';
                  btn.disabled = false;
                  return;
              }
              alert('تم تفعيل الامتحان بنجاح.');
              location.reload();
          })
          .catch(function () {
              errorBox.textContent = 'حصل خطأ في الاتصال، حاول تاني.';
              errorBox.style.display = 'block';
              btn.disabled = false;
          });
      });
  })();
  </script>

  <script>
  (function () {
      var csrfToken = document.querySelector('meta[name="csrf-token"]');
      csrfToken = csrfToken ? csrfToken.content : '';
      var APP_URL = "{{ rtrim(url('/'), '/') }}";

      // ---- تحديث الكود كل 5 ثواني (نفس آلية manage.blade.php) ----
      function refreshPlacementCode() {
          var cell = document.querySelector('.code-cell[data-session-id]');
          if (!cell) return;

          var sessionId = cell.getAttribute('data-session-id');
          if (!sessionId) return;

          fetch(APP_URL + '/exam-sessions/' + sessionId + '/code', {
              headers: { 'Accept': 'application/json' }
          })
          .then(function (res) { return res.json(); })
          .then(function (data) {
              var codeEl = cell.querySelector('.code-value');
              if (codeEl && data.code) {
                  codeEl.textContent = data.code;
              }
          })
          .catch(function () {});
      }

      if (document.querySelector('.code-cell[data-session-id]')) {
          refreshPlacementCode();
          setInterval(refreshPlacementCode, 5000);
      }

      // ---- زرار إنهاء الجلسة ----
      var endBtn = document.getElementById('end-placement-btn');
      if (endBtn) {
          endBtn.addEventListener('click', function () {
              if (!confirm('متأكد إنك عايز تنهي الجلسة دي؟')) return;

              var sessionId = endBtn.getAttribute('data-session-id');
              endBtn.disabled = true;

              fetch(APP_URL + '/exam-sessions/' + sessionId + '/end', {
                  method: 'POST',
                  headers: {
                      'X-CSRF-TOKEN': csrfToken,
                      'Accept': 'application/json'
                  }
              })
              .then(function (res) { return res.json(); })
              .then(function () {
                  location.reload();
              })
              .catch(function () {
                  alert('حصل خطأ أثناء إنهاء الجلسة.');
                  endBtn.disabled = false;
              });
          });
      }
  })();
  </script>

  {{-- ============ باقي الامتحانات ============ --}}
  <div class="panel">
    <details class="section-toggle">
      <summary>الامتحانات والدرجات</summary>
      <div class="section-body">
        @if ($regularSubmissions->isEmpty())
          <div class="empty-state">مفيش امتحانات محلولة لسه.</div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>الامتحان</th>
                  <th>الدرجة</th>
                  <th>النسبة</th>
                  <th>الوقت المستغرق</th>
                  <th>تاريخ التسليم</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($regularSubmissions as $submission)
                  <tr>
                    <td>
                      {{ $submission->exam->title ?? '—' }}
                      @if ($submission->session && $submission->session->is_random_version)
                        <br><span class="badge-random">نسخة عشوائية</span>
                      @endif
                    </td>
                    <td>{{ $submission->score }} / {{ $submission->total_points }}</td>
                    <td>
                      @if ($submission->total_points > 0)
                        {{ round(($submission->score / $submission->total_points) * 100, 1) }}%
                      @else
                        —
                      @endif
                    </td>
                    <td>
                      @if ($submission->duration_seconds)
                        {{ gmdate('i:s', $submission->duration_seconds) }} دقيقة
                      @else
                        —
                      @endif
                    </td>
                    <td>{{ $submission->submitted_at ? $submission->submitted_at->format('j/n/Y - h:i A') : '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </details>
  </div>

  {{-- ============ الحضور والغياب ============ --}}
  <div class="panel">
    <details class="section-toggle">
      <summary>الحضور والغياب ({{ $attendances->count() }})</summary>
      <div class="section-body">
        @if ($attendances->isEmpty())
          <div class="empty-state">مفيش سجلات حضور مسجلة لسه.</div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>التاريخ</th>
                  <th>الجروب</th>
                  <th>الحالة</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($attendances as $attendance)
                  <tr>
                    <td>{{ $attendance->session ? $attendance->session->arabic_taken_at : '—' }}</td>
                    <td>{{ ($attendance->session && $attendance->session->group) ? $attendance->session->group->name : '—' }}</td>
                    <td>
                      <span class="status-pill {{ $attendance->status }}">
                        {{ $attendance->status_label }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </details>
  </div>

  {{-- ============ التقييمات ============ --}}
  <div class="panel">
    <details class="section-toggle">
      <summary>تقييمات المهارات</summary>
      <div class="section-body">
        @if ($evaluations->isEmpty())
          <div class="empty-state">
            مفيش تقييمات مسجلة لسه — هتتضاف تقييمات المهارات من المدرب في نهاية كل مستوى.
          </div>
        @else
          @foreach ($evaluations as $evaluation)
            <div class="evaluation-card">
              <div class="top">
                <span class="skill">{{ $evaluation->skill }}</span>
                @if ($evaluation->level)
                  <span class="level-tag">مستوى {{ $evaluation->level }}</span>
                @endif
              </div>
              @if ($evaluation->rating)
                <div style="font-weight:700; color:var(--navy-900,#0b1f45); margin-bottom:4px;">
                  التقييم: {{ $evaluation->rating }} / 10
                </div>
              @endif
              @if ($evaluation->comment)
                <div class="comment">{{ $evaluation->comment }}</div>
              @endif
              <div class="meta">
                بواسطة {{ $evaluation->evaluator ? $evaluation->evaluator->name : '—' }} — {{ $evaluation->created_at->format('j/n/Y') }}
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </details>
  </div>

</div>

@endsection
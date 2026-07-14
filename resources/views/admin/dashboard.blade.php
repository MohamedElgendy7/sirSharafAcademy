@extends('layouts.app')

@section('title', 'لوحة التحكم — Sir Sharaf Academy')

@section('content')
  <div class="topbar">
    <div>
      <h1>أهلاً بيك، {{ Auth::user()->name ?? 'أستاذ' }} 👋</h1>
      <p>نظرة عامة على أداء الأكاديمية اليوم</p>
    </div>
    <div class="top-actions">
      <button class="btn btn-ghost">تصدير تقرير</button>
      <button class="btn btn-primary">+ تسجيل طالب جديد</button>
    </div>
  </div>

  <div class="stat-grid">
    <div class="stat-card">
      <div class="top">
        <div class="stat-icon" style="background:#eef2fb; color:var(--navy-800);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/></svg>
        </div>
      </div>
      <div class="val">312</div>
      <div class="lbl">إجمالي الطلاب</div>
      <div class="delta up">▲ 4.2% عن الشهر اللي فات</div>
    </div>

    <div class="stat-card">
      <div class="top">
        <div class="stat-icon" style="background:#fdecec; color:var(--red-700);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
        </div>
      </div>
      <div class="val">18</div>
      <div class="lbl">طلبات تسجيل جديدة</div>
      <div class="delta up">▲ 2 اليوم</div>
    </div>

    <div class="stat-card">
      <div class="top">
        <div class="stat-icon" style="background:#eaf3de; color:#3b6d11;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
        </div>
      </div>
      <div class="val">42,300 ج.م</div>
      <div class="lbl">إيرادات الشهر</div>
      <div class="delta up">▲ 8.1%</div>
    </div>

    <div class="stat-card">
      <div class="top">
        <div class="stat-icon" style="background:#faeeda; color:#854f0b;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11H3v10h6V11ZM21 3h-6v18h6V3ZM15 8H9v13h6V8Z"/></svg>
        </div>
      </div>
      <div class="val">14</div>
      <div class="lbl">عدد المعلمين</div>
      <div class="delta">مستقر</div>
    </div>
  </div>

  <div class="panels">
    <div class="panel">
      <h3>آخر الطلاب المسجلين</h3>
      <div class="row">
        <div class="dot" style="background:#1a9c5c;"></div>
        <div class="name">أحمد محمود</div>
        <div class="sub">اليوم، 10:30 ص</div>
      </div>
      <div class="row">
        <div class="dot" style="background:#1a9c5c;"></div>
        <div class="name">سارة علي</div>
        <div class="sub">أمس، 4:15 م</div>
      </div>
      <div class="row">
        <div class="dot" style="background:#e0a20c;"></div>
        <div class="name">يوسف كمال</div>
        <div class="sub">أمس، 1:02 م</div>
      </div>
    </div>

    <div class="panel">
      <h3>تنبيهات</h3>
      <div class="row">
        <span class="tag" style="background:#fdecec; color:var(--red-700);">متأخر</span>
        <div class="name">5 فواتير متأخرة السداد</div>
      </div>
      <div class="row">
        <span class="tag" style="background:#faeeda; color:#854f0b;">مراجعة</span>
        <div class="name">3 طلبات انتظار جديدة</div>
      </div>
    </div>
  </div>
@endsection

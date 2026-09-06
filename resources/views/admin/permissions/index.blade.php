@extends('layouts.app') {{-- غيّر اسم الـ layout لو مختلف عندك --}}

@section('content')
<div class="permissions-page" dir="rtl">
  <h1>إدارة صلاحيات الموظفين</h1>
  <p class="hint">حدد الأقسام اللي كل موظف يقدر يشوفها في السايد بار. الأدمن دايمًا بيشوف كل حاجة تلقائي.</p>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif

  @if($admins->isEmpty())
    <p>لا يوجد موظفين حاليًا.</p>
  @endif

  @foreach($admins as $admin)
    @php
      $isFirstAdmin = $loop->first;
    @endphp
    <div class="permission-accordion-item {{ $isFirstAdmin ? 'open' : '' }}">
      <button class="permission-accordion-toggle" type="button" aria-expanded="{{ $isFirstAdmin ? 'true' : 'false' }}">
        <span class="admin-info">
          <span class="admin-role-badge">{{ $admin->role === 'admin' ? 'أدمن' : 'موظف' }}</span>
          <strong>{{ $admin->name }}</strong>
          <span class="muted">{{ $admin->email }}</span>
        </span>
        <span class="toggle-icon">+</span>
      </button>

      <div class="permission-accordion-panel" {{ $isFirstAdmin ? '' : 'hidden' }}>
        <form method="POST" action="{{ route('admin.permissions.update', $admin) }}" class="permission-card">
          @csrf
          @method('PUT')

          @foreach($permissionGroups as $groupName => $permissions)
            <div class="permission-group">
              <div class="permission-group-title">{{ $groupName }}</div>
              <div class="permission-group-items">
                @foreach($permissions as $key => $label)
                  <label class="permission-checkbox">
                    <input type="checkbox" name="{{ $key }}" value="1"
                      {{ optional($admin->permissions)->{$key} ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach

          <button type="submit" class="btn-save">حفظ صلاحيات {{ $admin->name }}</button>
        </form>
      </div>
    </div>
  @endforeach
</div>

<style>
  .permissions-page { max-width: 980px; margin: 0 auto; padding: 30px 20px 40px; font-family: inherit; }
  .permissions-page h1 {
    margin: 0 0 8px; font-size: 2rem; color: #111827;
  }
  .permissions-page .hint {
    color: #6b7280; margin-bottom: 24px; line-height: 1.7;
  }

  .alert {
    padding: 12px 14px; border-radius: 10px; margin-bottom: 18px; font-weight: 600;
  }
  .alert-success { background: #dcfce7; color: #166534; }
  .alert-error { background: #fee2e2; color: #991b1b; }

  .permission-accordion-item {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 4px 10px rgba(17, 24, 39, 0.03);
  }

  .permission-accordion-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: none;
    background: linear-gradient(180deg, #f8fafc 0%, #f3f4f6 100%);
    padding: 18px 20px;
    cursor: pointer;
    font-size: 1rem;
    text-align: right;
    color: #111827;
  }

  .permission-accordion-toggle:hover {
    background: linear-gradient(180deg, #f1f5f9 0%, #e5e7eb 100%);
  }

  .admin-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: flex-start;
  }

  .admin-role-badge {
    display: inline-block;
    background: #e0f2fe;
    color: #075985;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 999px;
    margin-bottom: 2px;
  }

  .permission-accordion-toggle strong {
    font-size: 1.08rem;
  }

  .permission-accordion-toggle .muted {
    color: #6b7280;
    font-size: 0.9em;
  }

  .toggle-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid #d1d5db;
    font-size: 1.7rem;
    line-height: 1;
    color: #374151;
    transition: transform 0.2s ease;
  }

  .permission-accordion-item.open .toggle-icon {
    transform: rotate(45deg);
    background: #111827;
    color: #fff;
    border-color: #111827;
  }

  .permission-accordion-panel {
    padding: 0 20px 20px;
    background: #fff;
  }

  .permission-card {
    border-top: 1px solid #eef2f7;
    padding-top: 18px;
  }

  .permission-group {
    margin-bottom: 16px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 14px;
  }

  .permission-group-title {
    font-weight: 700;
    margin-bottom: 10px;
    color: #374151;
  }

  .permission-group-items {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px 12px;
  }

  .permission-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95em;
    color: #1f2937;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 8px 10px;
  }

  .permission-checkbox input {
    accent-color: #111827;
    width: 16px;
    height: 16px;
    margin: 0;
  }

  .permission-checkbox span {
    line-height: 1.5;
  }

  .btn-save {
    margin-top: 10px; background: #111827; color: #fff; border: none;
    padding: 10px 20px; border-radius: 10px; cursor: pointer;
    font-weight: 600;
    transition: background 0.2s ease;
  }
  .btn-save:hover { background: #1f2937; }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.permission-accordion-toggle');

    toggles.forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        const item = this.closest('.permission-accordion-item');
        const panel = item.querySelector('.permission-accordion-panel');
        const isOpen = item.classList.contains('open');

        item.classList.toggle('open', !isOpen);
        this.setAttribute('aria-expanded', String(!isOpen));
        panel.hidden = isOpen;
      });
    });
  });
</script>
@endsection

@extends('layouts.admin') {{-- غيّر اسم الـ layout لو مختلف عندك --}}

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

  @if($employees->isEmpty())
    <p>لا يوجد موظفين حاليًا.</p>
  @endif

  @foreach($employees as $employee)
    <form method="POST" action="{{ route('admin.permissions.update', $employee) }}" class="permission-card">
      @csrf
      @method('PUT')

      <div class="permission-card-header">
        <strong>{{ $employee->name }}</strong>
        <span class="muted">{{ $employee->email }}</span>
      </div>

      @foreach($permissionGroups as $groupName => $permissions)
        <div class="permission-group">
          <div class="permission-group-title">{{ $groupName }}</div>
          <div class="permission-group-items">
            @foreach($permissions as $key => $label)
              <label class="permission-checkbox">
                <input type="checkbox" name="{{ $key }}" value="1"
                  {{ $employee->{$key} ? 'checked' : '' }}>
                {{ $label }}
              </label>
            @endforeach
          </div>
        </div>
      @endforeach

      <button type="submit" class="btn-save">حفظ صلاحيات {{ $employee->name }}</button>
    </form>
  @endforeach
</div>

<style>
  .permissions-page { max-width: 900px; margin: 0 auto; padding: 24px; font-family: inherit; }
  .permissions-page h1 { margin-bottom: 4px; }
  .permissions-page .hint { color: #6b7280; margin-bottom: 20px; }
  .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; }
  .alert-success { background: #dcfce7; color: #166534; }
  .alert-error { background: #fee2e2; color: #991b1b; }
  .permission-card {
    border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px;
    margin-bottom: 18px; background: #fff;
  }
  .permission-card-header { display: flex; gap: 10px; align-items: baseline; margin-bottom: 14px; }
  .permission-card-header .muted { color: #9ca3af; font-size: .9em; }
  .permission-group { margin-bottom: 14px; }
  .permission-group-title { font-weight: 600; margin-bottom: 8px; color: #374151; }
  .permission-group-items { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; }
  .permission-checkbox { display: flex; align-items: center; gap: 6px; font-size: .95em; }
  .btn-save {
    margin-top: 10px; background: #111827; color: #fff; border: none;
    padding: 8px 18px; border-radius: 8px; cursor: pointer;
  }
  .btn-save:hover { background: #1f2937; }
</style>
@endsection

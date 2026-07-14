@extends('layouts.app')

@section('title', 'الصلاحيات والأدوار — Sir Sharaf Academy')

@section('content')
  <div class="topbar">
    <div>
      <h1>الأدوار والصلاحيات</h1>
      <p>حدد اللي كل دور يقدر يعمله، وغيّرها وقت ما تحب</p>
    </div>
  </div>

  @if (session('success'))
    <div style="background:#eaf3de; color:#3b6d11; border-radius:10px; padding:12px 16px; margin-bottom:18px; font-size:13px;">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div style="background:#fdecec; color:var(--red-700); border-radius:10px; padding:12px 16px; margin-bottom:18px; font-size:13px;">
      {{ session('error') }}
    </div>
  @endif

  <div style="background:var(--paper-card); border:1px solid var(--border); border-radius:var(--radius); padding:16px 18px; margin-bottom:18px; font-size:13px; color:var(--ink-500);">
    <strong style="color:var(--ink-900);">مدير المركز (super-admin)</strong> عنده كل الصلاحيات تلقائي وثابت، مش هيظهر في القائمة تحت عشان محدش يقدر يقلل صلاحياته بالغلط.
  </div>

  @foreach ($roles as $role)
    <div class="panel" style="margin-bottom:16px;">
      <h3>{{ $role->name === 'receptionist' ? 'موظف استقبال' : $role->name }}</h3>

      <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:10px; margin-bottom:16px;">
          @foreach ($permissions as $permission)
            <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--ink-900); cursor:pointer;">
              <input
                type="checkbox"
                name="permissions[]"
                value="{{ $permission->name }}"
                {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}
              >
              {{ $permission->name }}
            </label>
          @endforeach
        </div>

        <button type="submit" class="btn btn-primary">حفظ صلاحيات "{{ $role->name }}"</button>
      </form>
    </div>
  @endforeach
@endsection

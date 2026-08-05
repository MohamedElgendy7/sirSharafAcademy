<form method="POST" action="{{ route('admin.permissions.update', $userRow) }}" class="permission-card">
  @csrf
  @method('PUT')

  <div class="permission-card-header">
    <strong>{{ $userRow->name }}</strong>
    <span class="muted">{{ $userRow->email }}</span>
    <span class="muted">({{ $userRow->role === 'admin' ? 'أدمن' : 'موظف' }})</span>
  </div>

  @foreach($permissionGroups as $groupName => $permissions)
    <div class="permission-group">
      <div class="permission-group-title">{{ $groupName }}</div>
      <div class="permission-group-items">
        @foreach($permissions as $key => $label)
          <label class="permission-checkbox">
            <input type="checkbox" name="{{ $key }}" value="1"
              {{ optional($userRow->permissions)->{$key} ? 'checked' : '' }}>
            {{ $label }}
          </label>
        @endforeach
      </div>
    </div>
  @endforeach

  <button type="submit" class="btn-save">حفظ صلاحيات {{ $userRow->name }}</button>
</form>

@if($students->count())
  <div class="requests-list">
    <table class="requests-table table-striped">
      <thead>
        <tr>
          <th>اسم الطالب</th>
          <th>رقم الهاتف</th>
          <th>الكورس</th>
          <th>المستوى</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($students as $student)
          <tr>
            <td class="name-cell">{{ $student->name }}</td>
            <td>{{ $student->phone }}</td>
            <td>{{ $student->course ?? '—' }}</td>
            <td>{{ $student->level ?? '—' }}</td>
            <td class="action-cell">
              <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">تعديل الملف</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@else
  <div class="empty-state">لا يوجد طلاب حاليًا</div>
@endif

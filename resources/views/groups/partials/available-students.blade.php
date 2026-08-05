@if($search)
  @if($availableStudents && $availableStudents->count())
    <div class="list-wrap">
      <table class="tbl">
        <tbody>
          @foreach($availableStudents as $student)
            <tr>
              <td class="name-cell">{{ $student->name }}</td>
              <td>{{ $student->phone }}</td>
              <td class="action-cell">
                <form method="POST" action="{{ route('groups.addStudent', $group) }}">
                  @csrf
                  <input type="hidden" name="student_id" value="{{ $student->id }}">
                  <button type="submit" class="btn btn-primary">إضافة</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="empty-state">مفيش نتائج مطابقة، أو الطالب مضاف بالفعل للجروب</div>
  @endif
@else
  <div class="empty-state">اكتب اسم أو رقم هاتف عشان تبحث عن طالب تضيفه</div>
@endif

<div class="exams-table-wrapper" style="overflow-x:auto;">
    <table class="exams-table">
        <thead>
            <tr>
                <th>اسم الجروب</th>
                <th>الكورس</th>
                <th>المستوى</th>
                <th>عدد الطلاب</th>
                <th>الحالة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                <tr>
                    <td>{{ $group->name }}</td>
                    <td>{{ $group->course }}</td>
                    <td>{{ $group->level }}</td>
                    <td>{{ $group->students_count }} / {{ $group->capacity }}</td>
                    <td>
                        <span class="tag {{ $group->status }}">
                            {{ $group->status == 'active' ? 'Active' : 'انتهى' }}
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('groups.show', $group) }}" class="btn-sm-filled btn-view">عرض الجروب</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="color: var(--ink-500);">لا يوجد جروبات مطابقة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

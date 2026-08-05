<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSession;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * صفحة اختيار الجروب لأخد الحضور (بتظهر من عنصر "الحضور والغياب" في السايد بار)
     */
    public function index()
    {
        $groups = Group::where('groups.status', 'active')
            ->withCount(['students' => function ($q) {
                $q->where('group_student.status', 'active');
            }])
            ->orderBy('name')
            ->get();

        return view('admin.attendance.index', compact('groups'));
    }

    /**
     * فورم أخد حضور جديد لجروب معين (حصة جديدة)
     */
    public function create(Group $group)
    {
        $students = $group->students()->where('group_student.status', 'active')->get();

        return view('admin.attendance.create', compact('group', 'students'));
    }

    /**
     * حفظ الحصة الجديدة + الحضور بتاعها
     */
    public function store(Request $request, Group $group)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
        ]);

        $session = GroupSession::create([
            'group_id' => $group->id,
            'taken_at' => now(),
            'created_by' => auth()->id(),
        ]);

        foreach ($request->attendance as $studentId => $status) {
            Attendance::create([
                'session_id' => $session->id,
                'student_id' => $studentId,
                'status' => $status,
                'recorded_by' => auth()->id(),
            ]);
        }

        return redirect()
            ->route('attendance.sessions', $group)
            ->with('success', 'تم تسجيل الحضور بنجاح');
    }

    /**
     * سجل كل الحصص السابقة لجروب معين
     */
    public function sessions(Group $group)
    {
        $sessions = $group->sessions()
            ->withCount('attendances')
            ->orderByDesc('taken_at')
            ->paginate(20);

        return view('admin.attendance.sessions', compact('group', 'sessions'));
    }

    /**
     * عرض/تعديل حضور حصة معينة
     */
    public function show(GroupSession $session)
    {
        $session->load('group', 'attendances.student');

        return view('admin.attendance.show', compact('session'));
    }

    /**
     * تعديل حضور حصة قديمة (من غير قيود زمنية)
     */
    public function update(Request $request, GroupSession $session)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
        ]);

        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate(
                ['session_id' => $session->id, 'student_id' => $studentId],
                ['status' => $status, 'recorded_by' => auth()->id()]
            );
        }

        return redirect()
            ->route('attendance.sessions.show', $session)
            ->with('success', 'تم تعديل الحضور بنجاح');
    }
}
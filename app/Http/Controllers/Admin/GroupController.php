<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * عرض كل الجروبات، مع إمكانية الفلترة بالاسم و/أو الكورس و/أو المستوى (منفردين أو مجتمعين).
     * بيدعم كمان طلبات AJAX (بحث لحظي من غير reload) عن طريق التحقق من X-Requested-With header.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $course = $request->query('course');
        $level  = $request->query('level');

        $groups = Group::withCount('students')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($course, function ($query, $course) {
                $query->where('course', $course);
            })
            ->when($level, function ($query, $level) {
                $query->where('level', $level);
            })
            ->latest()
            ->get();

        // لو الطلب جاي من الـ JavaScript (fetch) بنرجع جزء الجدول بس، من غير باقي الصفحة
        if ($request->ajax()) {
            return view('groups.partials.groups-table', compact('groups'));
        }

        return view('groups.index', compact('groups', 'search', 'course', 'level'));
    }

    /**
     * فورم إنشاء جروب جديد.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * حفظ الجروب الجديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'course'   => ['required', 'in:American Accent,Business English,General English,Conversation,IELTS preps,TOEFL preps'],
            'level'    => ['required', 'integer', 'between:1,10'],
            'capacity' => ['required', 'integer', 'between:8,15'],
        ]);

        $validated['status'] = 'active';

        $group = Group::create($validated);

        return redirect()
            ->route('groups.show', $group)
            ->with('success', 'تم إنشاء الجروب بنجاح، دلوقتي تقدر تضيفله طلاب.');
    }

    /**
     * عرض جروب واحد: بياناته + طلابه + بحث لإضافة طلاب جدد.
     * بيدعم كمان طلبات AJAX لبحث "إضافة طالب للجروب" (بحث لحظي من غير reload).
     */
    public function show(Request $request, Group $group)
    {
        $group->load(['students' => function ($query) {
            $query->orderBy('name');
        }]);

        $search = $request->query('search');
        $availableStudents = collect();

        if ($search) {
            $existingIds = $group->students->pluck('id');

            $availableStudents = Student::where('status', 'active')
                ->whereNotIn('id', $existingIds)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get();
        }

        // لو الطلب جاي من الـ JavaScript (fetch) بنرجع جزء نتايج البحث بس، من غير باقي الصفحة
        if ($request->ajax()) {
            return view('groups.partials.available-students', compact('availableStudents', 'search', 'group'));
        }

        return view('groups.show', compact('group', 'search', 'availableStudents'));
    }

    /**
     * إضافة طالب للجروب.
     */
    public function addStudent(Request $request, Group $group)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        if ($group->students()->count() >= $group->capacity) {
            return redirect()
                ->route('groups.show', $group)
                ->with('error', 'الجروب وصل للعدد الأقصى المسموح به ('.$group->capacity.' طالب).');
        }

        if (! $group->students()->where('student_id', $request->student_id)->exists()) {
            $group->students()->attach($request->student_id, [
                'joined_at' => now(),
                'status'    => 'active',
            ]);
        }

        return redirect()
            ->route('groups.show', $group)
            ->with('success', 'تمت إضافة الطالب للجروب بنجاح.');
    }

    /**
     * حذف طالب من الجروب.
     */
    public function removeStudent(Group $group, Student $student)
    {
        $group->students()->detach($student->id);

        return redirect()
            ->route('groups.show', $group)
            ->with('success', 'تم حذف الطالب من الجروب.');
    }
}

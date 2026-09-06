<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Level;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * قاعدة فاليديشن الكورس والمستوى المشتركة بين store() و update():
     * الكورس لازم يكون اسم موجود فعليًا في جدول courses، والمستوى لازم
     * يكون اسم موجود في جدول levels ومربوط بنفس الكورس المختار بالظبط.
     */
    protected function courseAndLevelRules(Request $request)
    {
        return [
            'course' => ['nullable', 'exists:courses,name'],
            'level' => [
                'nullable',
                'string',
                Rule::exists('levels', 'name')->where(function ($query) use ($request) {
                    $course = Course::where('name', $request->input('course'))->first();
                    $query->where('course_id', $course ? $course->id : 0);
                }),
            ],
        ];
    }

    /**
     * عرض فورم تسجيل طالب جديد (طلب تسجيل جديد).
     */
    public function create()
    {
        $courses = Course::orderBy('name')->get();

        return view('students.create', compact('courses'));
    }

    /**
     * حفظ بيانات الطالب الجديد في الداتابيز.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(array_merge([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'age'            => ['nullable', 'integer', 'min:3', 'max:100'],
            'gender'         => ['required', 'in:male,female'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'branch'         => ['nullable', 'in:cairo,tanta,kafr_elsheikh,online'],
        ], $this->courseAndLevelRules($request)));

        // أي طالب بيتسجل من الفورم ده بيبقى "طلب تسجيل جديد" لحد ما يتم اعتماده
        $validated['status'] = 'pending';

        Student::create($validated);

        return redirect()
            ->route('students.create')
            ->with('success', 'تم تسجيل طلب الطالب بنجاح، وهيظهر في قائمة طلبات التسجيل الجديدة.');
    }

    /**
     * عرض قائمة طلبات التسجيل الجديدة (status = pending)، مع إمكانية البحث بالاسم أو رقم الهاتف.
     * بيدعم كمان طلبات AJAX (بحث لحظي من غير reload) عن طريق التحقق من X-Requested-With header.
     */
    public function pending(Request $request)
    {
        $search = $request->query('search');

        $students = Student::where('status', 'pending')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('whatsapp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // لو الطلب جاي من الـ JavaScript (fetch) بنرجع جزء الجدول بس، من غير باقي الصفحة
        if ($request->ajax()) {
            return view('students.partials.pending-table', compact('students'));
        }

        return view('students.pending', compact('students', 'search'));
    }

    /**
     * عرض قائمة الطلاب الحاليين (status = active)، مع إمكانية البحث بالاسم أو رقم الهاتف.
     * بيدعم كمان طلبات AJAX (بحث لحظي من غير reload) عن طريق التحقق من X-Requested-With header.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $students = Student::where('status', 'active')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('whatsapp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        // لو الطلب جاي من الـ JavaScript (fetch) بنرجع جزء الجدول بس، من غير باقي الصفحة
        if ($request->ajax()) {
            return view('students.partials.active-table', compact('students'));
        }

        return view('students.index', compact('students', 'search'));
    }

    /**
     * عرض ملف طالب واحد بكل بياناته (بدون إمكانية تعديل).
     */
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    /**
     * بروفايل شامل للطالب النشط: بيانات أساسية + درجات الامتحانات (مع تمييز
     * امتحان تحديد المستوى) + الحضور والغياب + تقييمات المهارات + ملخص سريع.
     */
    public function profile(Student $student)
    {
        $submissions = $student->examSubmissions()
            ->with('exam')
            ->latest('submitted_at')
            ->get();

        // امتحان/امتحانات تحديد المستوى تتعرض منفصلة ومميزة عن باقي الامتحانات العادية
        $placementSubmissions = $submissions->filter(function ($s) {
            return $s->exam && $s->exam->type === 'placement';
        });
        $regularSubmissions = $submissions->filter(function ($s) {
            return ! $s->exam || $s->exam->type !== 'placement';
        });

        $attendances = $student->attendances()
            ->with('session.group')
            ->get()
            ->sortByDesc(function ($a) {
                return $a->session ? $a->session->taken_at : null;
            });

        $evaluations = $student->evaluations()
            ->with('evaluator')
            ->latest()
            ->get();

        $currentGroup = $student->groups()
            ->wherePivot('status', 'active')
            ->first();

        // كل امتحانات تحديد المستوى المتاحة، عشان الأدمن يقدر يفعّل واحد منها من نفس الصفحة
        $placementExams = \App\Models\Exam::where('type', 'placement')->orderBy('title')->get();

        // لو الطالب عنده جلسة تحديد مستوى شغالة دلوقتي (اتفعّلت بس لسه معملش تسليم)
        $activePlacementSession = $student->examSessions()
            ->where('status', 'active')
            ->whereHas('exam', function ($q) {
                $q->where('type', 'placement');
            })
            ->with('exam')
            ->latest()
            ->first();

        // ---- إحصائيات الملخص السريع ----
        $examsCount = $submissions->count();

        $scoredSubmissions = $submissions->filter(fn ($s) => $s->total_points > 0);
        $averageScorePercent = $scoredSubmissions->isNotEmpty()
            ? round($scoredSubmissions->avg(fn ($s) => ($s->score / $s->total_points) * 100), 1)
            : null;

        $attendanceTotal = $attendances->count();
        $attendancePresentOrLate = $attendances->whereIn('status', ['present', 'late'])->count();
        $attendancePercent = $attendanceTotal > 0
            ? round(($attendancePresentOrLate / $attendanceTotal) * 100, 1)
            : null;

        return view('students.profile', compact(
            'student',
            'placementSubmissions',
            'regularSubmissions',
            'attendances',
            'evaluations',
            'currentGroup',
            'examsCount',
            'averageScorePercent',
            'attendancePercent',
            'placementExams',
            'activePlacementSession'
        ));
    }

    /**
     * عرض فورم تعديل بيانات طالب موجود بالفعل (Active).
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * حفظ تعديلات بيانات الطالب.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate(array_merge([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'age'            => ['nullable', 'integer', 'min:3', 'max:100'],
            'gender'         => ['required', 'in:male,female'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'branch'         => ['nullable', 'in:cairo,tanta,kafr_elsheikh,online'],
        ], $this->courseAndLevelRules($request)));

        $student->update($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'تم حفظ تعديلات بيانات الطالب "'.$student->name.'" بنجاح.');
    }

    /**
     * اعتماد طلب التسجيل: تغيير حالة الطالب من pending إلى active، مع حفظ أي ملاحظة.
     */
    public function approve(Request $request, Student $student)
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $student->update([
            'status' => 'active',
            'notes'  => $request->notes,
        ]);

        return redirect()
            ->route('students.pending')
            ->with('success', 'تم اعتماد تسجيل الطالب "'.$student->name.'" بنجاح، وأصبح ضمن الطلاب الحاليين.');
    }

    /**
     * رفض طلب التسجيل: تغيير حالة الطالب إلى rejected، مع حفظ سبب الرفض في الملاحظات.
     */
    public function reject(Request $request, Student $student)
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $student->update([
            'status' => 'pending',
            'notes'  => $request->notes,
        ]);

        return redirect()
            ->route('students.pending')
            ->with('success', 'تم رفض طلب الطالب "'.$student->name.'".');
    }
}
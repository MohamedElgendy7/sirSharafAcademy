<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * عرض فورم تسجيل طالب جديد (طلب تسجيل جديد).
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * حفظ بيانات الطالب الجديد في الداتابيز.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'age'            => ['nullable', 'integer', 'min:3', 'max:100'],
            'gender'         => ['required', 'in:male,female'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'branch'         => ['nullable', 'in:cairo,tanta,kafr_elsheikh,online'],
            'course'         => ['nullable', 'in:American Accent,Business English,General English,Conversation,IELTS preps,TOEFL preps'],
            'level'          => ['nullable', 'integer', 'between:1,10'],
        ]);

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
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:255'],
            'age'            => ['nullable', 'integer', 'min:3', 'max:100'],
            'gender'         => ['required', 'in:male,female'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'branch'         => ['nullable', 'in:cairo,tanta,kafr_elsheikh,online'],
            'course'         => ['nullable', 'in:American Accent,Business English,General English,Conversation,IELTS preps,TOEFL preps'],
            'level'          => ['nullable', 'integer', 'between:1,10'],
        ]);

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

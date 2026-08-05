<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamChoice;
use App\Models\ExamQuestion;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    /**
     * قائمة كل الامتحانات
     */
    public function index()
    {
        $exams = Exam::withCount('questions')->latest()->get();

        return view('exams.index', compact('exams'));
    }

    /**
     * فورم إنشاء امتحان جديد
     */
    public function create()
    {
       $courses = Course::orderBy('name')->get();

       return view('exams.create', compact('courses'));
    }

    /**
     * حفظ الامتحان الجديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:regular,placement',
            'course_id' => 'required_if:type,regular|nullable|exists:courses,id',
            'level_id' => 'required_if:type,regular|nullable|exists:levels,id',
            'points_mode' => 'required|in:uniform,custom',
            'uniform_points' => 'nullable|integer|min:1',
        ]);

        // تأكيد إضافي في السيرفر: امتحان تحديد المستوى مالوش كورس أو مستوى
        // مهم حتى لو الفرونت حاول يبعتهم بأي شكل
        if ($validated['type'] === 'placement') {
            $validated['course_id'] = null;
            $validated['level_id'] = null;
        }

        $validated['created_by'] = Auth::id();

        $exam = Exam::create($validated);

        return redirect()
            ->route('exams.edit', $exam)
            ->with('success', 'تم إنشاء الامتحان، دلوقتي ضيف الأسئلة.');
    }

    /**
     * فورم تعديل الامتحان + إدارة الأسئلة والاختيارات
     */
    public function edit(Exam $exam)
    {
        $exam->load('questions.choices');
        $groups = Group::where('status', 'active')->get();

        return view('exams.edit', compact('exam', 'groups'));
    }

    /**
     * تحديث بيانات الامتحان الأساسية
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'type' => 'required|in:regular,placement',
            'points_mode' => 'required|in:uniform,custom',
            'uniform_points' => 'nullable|required_if:points_mode,uniform|integer|min:1',
        ]);

        $exam->update($validated);

        return redirect()
            ->route('exams.edit', $exam)
            ->with('success', 'تم تحديث بيانات الامتحان.');
    }

    /**
     * حذف الامتحان بالكامل
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()
            ->route('exams.index')
            ->with('success', 'تم حذف الامتحان.');
    }

    /**
     * نسخ الامتحان بكل أسئلته واختياراته
     */
    public function duplicate(Exam $exam)
    {
        $exam->load('questions.choices');

        $newExam = $exam->replicate();
        $newExam->title = $exam->title . ' (نسخة)';
        $newExam->created_by = Auth::id();
        $newExam->save();

        foreach ($exam->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->exam_id = $newExam->id;
            $newQuestion->save();

            foreach ($question->choices as $choice) {
                $newChoice = $choice->replicate();
                $newChoice->question_id = $newQuestion->id;
                $newChoice->save();
            }
        }

        return redirect()
            ->route('exams.edit', $newExam)
            ->with('success', 'تم نسخ الامتحان بنجاح.');
    }

    /**
     * إضافة سؤال جديد للامتحان
     */
    public function storeQuestion(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'points' => 'nullable|integer|min:1',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp3,wav,mp4,mov|max:20480',
        ]);

        $points = $validated['points'] ?? 1;

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('exam-attachments', 'public');

            $mime = $file->getMimeType();
            if (str_starts_with($mime, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mime, 'audio/')) {
                $attachmentType = 'audio';
            } elseif (str_starts_with($mime, 'video/')) {
                $attachmentType = 'video';
            }
        }

        $question = $exam->questions()->create([
            'question_text' => $validated['question_text'],
            'points' => $points,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'order' => $exam->questions()->max('order') + 1,
        ]);

        return response()->json([
            'message' => 'تم إضافة السؤال.',
            'question' => $question->load('choices'),
            'attachment_url' => $question->attachment_url,
        ], 201);
    }

    /**
     * حذف سؤال
     */
    public function destroyQuestion(ExamQuestion $question)
    {
        if ($question->attachment_path) {
            Storage::disk('public')->delete($question->attachment_path);
        }

        // حذف صريح للاختيارات الأول (تحصين إضافي فوق onDelete cascade في الداتابيز)
        $question->choices()->delete();

        $question->delete();

        return response()->json(['message' => 'تم حذف السؤال.']);
    }

    /**
     * إضافة اختيار لسؤال معين
     */
    public function storeChoice(Request $request, ExamQuestion $question)
    {
        $validated = $request->validate([
            'choice_text' => 'required|string|max:255',
            'is_correct' => 'nullable|boolean',
        ]);

        // لو الاختيار الجديد هيبقى الصح، نشيل الصح من الباقي (إجابة واحدة صحيحة بس)
        if ($request->boolean('is_correct')) {
            $question->choices()->update(['is_correct' => false]);
        }

        $choice = $question->choices()->create([
            'choice_text' => $validated['choice_text'],
            'is_correct' => $request->boolean('is_correct'),
        ]);

        return response()->json([
            'message' => 'تم إضافة الاختيار.',
            'choice' => $choice,
            'question_id' => $question->id,
            'unset_others' => $request->boolean('is_correct'),
        ], 201);
    }

    /**
     * حذف اختيار
     */
    public function destroyChoice(ExamChoice $choice)
    {
        $choice->delete();

        return response()->json(['message' => 'تم حذف الاختيار.']);
    }
    public function levelsByCourse(Course $course)
    {
        return response()->json(
            $course->levels()->orderBy('id')->get(['id', 'name'])
        );
    }
}

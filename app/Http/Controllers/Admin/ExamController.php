<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamChoice;
use App\Models\ExamQuestion;
use App\Models\ExamSession;
use App\Models\ExamSubmission;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ExamVersionGroup;



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
        $versionGroups = ExamVersionGroup::orderBy('name')->get();

        return view('exams.create', compact('courses', 'versionGroups'));
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
            'version_group_id' => 'nullable|exists:exam_version_groups,id',
            'points_mode' => 'required|in:uniform,custom',
            'uniform_points' => 'nullable|integer|min:1',
        ]);

        // تأكيد إضافي في السيرفر: امتحان تحديد المستوى مالوش كورس أو مستوى
        // مهم حتى لو الفرونت حاول يبعتهم بأي شكل
        if ($validated['type'] === 'placement') {
            $validated['course_id'] = null;
            $validated['level_id'] = null;
        } else {
            $validated['version_group_id'] = null;
        }

        $validated['created_by'] = Auth::id();

        $exam = Exam::create($validated);

         return redirect()->route('exams.edit', $exam)
            ->with('success', 'تم إنشاء الامتحان، دلوقتي ضيف الأسئلة.');
}

    /**
     * فورم تعديل الامتحان + إدارة الأسئلة والاختيارات
     */
    public function edit(Exam $exam)
    {
        $exam->load('questions.choices');
        $groups = Group::where('status', 'active')->get();
        $courses = Course::orderBy('name')->get();
        $versionGroups = ExamVersionGroup::orderBy('name')->get();

        return view('exams.edit', compact('exam', 'groups', 'courses', 'versionGroups'));
    }

    /**
     * تحديث بيانات الامتحان الأساسية
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:regular,placement',
            'course_id' => 'required_if:type,regular|nullable|exists:courses,id',
            'level_id' => 'required_if:type,regular|nullable|exists:levels,id',
            'version_group_id' => 'nullable|exists:exam_version_groups,id',
            'points_mode' => 'required|in:uniform,custom',
            'uniform_points' => 'nullable|required_if:points_mode,uniform|integer|min:1',
        ]);

        // نفس التأكيد اللي في store(): امتحان تحديد المستوى مالوش كورس أو مستوى، والعكس
        if ($validated['type'] === 'placement') {
            $validated['course_id'] = null;
            $validated['level_id'] = null;
        } else {
            $validated['version_group_id'] = null;
        }

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

    public function manage(Exam $exam, Group $group)
    {
        $students = $group->students()
            ->wherePivot('status', 'active')
            ->get();

        // لو الامتحان تابع لمجموعة نسخ، نجيب جلسات كل نسخ المجموعة (مش النسخة دي بس)
        // عشان لو طالب اتفعّله نسخة عشوائية مختلفة، الجدول يوريها صح مش "لسه متفعّلش"
        $examIds = $exam->version_group_id
            ? Exam::where('version_group_id', $exam->version_group_id)->pluck('id')
            : collect([$exam->id]);

        $sessions = ExamSession::whereIn('exam_id', $examIds)
            ->where('group_id', $group->id)
            ->with('exam')
            ->get()
            ->keyBy('student_id');

        return view('exam-sessions.manage', compact('exam', 'group', 'students', 'sessions'));
    }
 
    /**
     * تفعيل الامتحان لطالب واحد بس داخل جروب معين
     *
     * body اختياري:
     * - random_version: bool (لو الامتحان تابع لمجموعة نسخ، يختار نسخة عشوائية بدل $exam نفسه)
     * - time_limit_minutes: int|null (لو موجود، الامتحان يتقفل تلقائي بعد المدة دي من بدء الحل)
     */
    public function activateForStudent(Request $request, Exam $exam, Group $group, Student $student)
    {
        $request->validate([
            'random_version' => 'nullable|boolean',
            'time_limit_minutes' => 'nullable|integer|min:1',
        ]);
 
        $targetExam = $exam;
        $isRandom = false;
 
        if ($request->boolean('random_version') && $exam->version_group_id) {
            $randomPick = Exam::where('version_group_id', $exam->version_group_id)
                ->inRandomOrder()
                ->first();
 
            if ($randomPick) {
                $targetExam = $randomPick;
                $isRandom = true;
            }
        }
 
        // لو الامتحان تابع لمجموعة نسخ، نتحقق من عدم وجود جلسة نشطة على أي نسخة من نفس المجموعة
        // (مش بس نفس النسخة المحددة)، عشان الطالب مايقدرش ياخد نسختين مختلفتين في نفس الوقت
        $existingQuery = ExamSession::where('student_id', $student->id)->where('status', 'active');
 
        if ($targetExam->version_group_id) {
            $siblingIds = Exam::where('version_group_id', $targetExam->version_group_id)->pluck('id');
            $existingQuery->whereIn('exam_id', $siblingIds);
        } else {
            $existingQuery->where('exam_id', $targetExam->id);
        }
 
        if ($existingQuery->exists()) {
            return response()->json([
                'message' => 'الطالب عنده بالفعل جلسة امتحان نشطة',
            ], 409);
        }
 
        $session = $this->createSessionFor(
            $targetExam,
            $group,
            $student,
            $isRandom,
            $request->input('time_limit_minutes')
        );
 
        return response()->json([
            'message' => 'تم تفعيل الامتحان للطالب بنجاح',
            'session' => $session,
        ], 201);
    }
 
    /**
     * تفعيل الامتحان لكل الطلاب النشطين (active) في الجروب دفعة واحدة
     */
    public function activateForGroup(Request $request, Exam $exam, Group $group)
    {
        $request->validate([
            'random_version' => 'nullable|boolean',
            'time_limit_minutes' => 'nullable|integer|min:1',
        ]);
 
        $useRandom = $request->boolean('random_version') && $exam->version_group_id;
        $timeLimitMinutes = $request->input('time_limit_minutes');
 
        // الطلاب النشطين في الجروب فقط (بفلترة pivot group_student.status = active)
        $students = $group->students()
            ->wherePivot('status', 'active')
            ->get();
 
        $siblingIds = $exam->version_group_id
            ? Exam::where('version_group_id', $exam->version_group_id)->pluck('id')
            : collect([$exam->id]);
 
        $created = [];
        $skipped = [];
 
        DB::transaction(function () use ($exam, $group, $students, $useRandom, $timeLimitMinutes, $siblingIds, &$created, &$skipped) {
            foreach ($students as $student) {
                $alreadyActive = ExamSession::where('student_id', $student->id)
                    ->where('status', 'active')
                    ->whereIn('exam_id', $siblingIds)
                    ->exists();
 
                if ($alreadyActive) {
                    $skipped[] = $student->id;
                    continue;
                }
 
                $targetExam = $exam;
                $isRandom = false;
 
                if ($useRandom) {
                    $randomPick = Exam::where('version_group_id', $exam->version_group_id)->inRandomOrder()->first();
                    if ($randomPick) {
                        $targetExam = $randomPick;
                        $isRandom = true;
                    }
                }
 
                $created[] = $this->createSessionFor($targetExam, $group, $student, $isRandom, $timeLimitMinutes);
            }
        });
 
        return response()->json([
            'message' => 'تم تفعيل الامتحان لكل طلاب الجروب',
            'activated_count' => count($created),
            'skipped_count' => count($skipped),
            'skipped_student_ids' => $skipped,
        ], 201);
    }
 
    /**
     * تفعيل امتحان تحديد المستوى لطالب من غير جروب خالص (بيتفعّل من بروفايل الطالب مباشرة).
     *
     * body اختياري:
     * - random_version: bool
     * - time_limit_minutes: int|null
     */
    public function activatePlacementForStudent(Request $request, Exam $exam, Student $student)
    {
        $request->validate([
            'random_version' => 'nullable|boolean',
            'time_limit_minutes' => 'nullable|integer|min:1',
        ]);

        if ($exam->type !== 'placement') {
            return response()->json(['message' => 'الامتحان ده مش من نوع تحديد المستوى.'], 422);
        }

        $targetExam = $exam;
        $isRandom = false;

        // "نسخة عشوائية" هنا معناها: أي امتحان نوعه placement في النظام كله،
        // مش بس اللي مربوطين بمجموعة نسخة واحدة تحديدًا (version_group_id).
        if ($request->boolean('random_version')) {
            $randomPick = Exam::where('type', 'placement')
                ->inRandomOrder()
                ->first();

            if ($randomPick) {
                $targetExam = $randomPick;
                $isRandom = true;
            }
        }

        // منع تكرار: الطالب مايكونش عنده جلسة نشطة على أي امتحان تحديد مستوى أصلًا،
        // مش بس على نفس النسخة المختارة (عشان مايحلش امتحانين تحديد مستوى مع بعض)
        $existingQuery = ExamSession::where('student_id', $student->id)
            ->where('status', 'active')
            ->whereHas('exam', function ($q) {
                $q->where('type', 'placement');
            });

        if ($existingQuery->exists()) {
            return response()->json([
                'message' => 'الطالب عنده بالفعل جلسة امتحان تحديد مستوى نشطة',
            ], 409);
        }

        $session = $this->createSessionFor(
            $targetExam,
            null,
            $student,
            $isRandom,
            $request->input('time_limit_minutes')
        );

        return response()->json([
            'message' => 'تم تفعيل امتحان تحديد المستوى للطالب بنجاح',
            'session' => $session,
        ], 201);
    }

    /**
     * إنشاء exam_session جديدة لطالب مع ترتيب عشوائي مستقل للأسئلة والاختيارات.
     * group ممكن تبقى null (امتحان تحديد المستوى مش مربوط بجروب).
     */
    protected function createSessionFor(Exam $exam, $group, Student $student, bool $isRandomVersion = false, ?int $timeLimitMinutes = null)
    {
        $questions = $exam->questions()->with('choices')->get();
 
        $questionOrder = $questions->pluck('id')->shuffle()->values()->all();
 
        $choicesOrder = [];
        foreach ($questions as $question) {
            $choicesOrder[$question->id] = $question->choices->pluck('id')->shuffle()->values()->all();
        }
 
        return ExamSession::create([
            'exam_id' => $exam->id,
            'group_id' => $group ? $group->id : null,
            'student_id' => $student->id,
            'is_random_version' => $isRandomVersion,
            'activated_by' => Auth::id(),
            'activated_at' => now(),
            'status' => 'active',
            'time_limit_minutes' => $timeLimitMinutes,
            'session_data' => [
                'question_order' => $questionOrder,
                'choices_order' => $choicesOrder,
            ],
        ]);
    }
 
    /**
     * الأدمن بيجيب الكود الحالي (بيتولد كود جديد تلقائي لو عدت الدقيقة)
     */
    public function getCode(ExamSession $examSession)
    {
        if ($examSession->status !== 'active') {
            return response()->json(['message' => 'الجلسة دي مش نشطة'], 422);
        }
 
        $code = $examSession->getOrRefreshCode();
 
        return response()->json([
            'code' => $code,
            'code_generated_at' => $examSession->code_generated_at,
            'valid_for_seconds' => 60 - $examSession->code_generated_at->diffInSeconds(now()),
        ]);
    }
 
    /**
     * الطالب بيدخل الكود عشان يبدأ الامتحان
     */
    public function verifyCode(Request $request, ExamSession $examSession)
    {
        $request->validate([
            'code' => 'required|string|size:8',
        ]);
 
        // تأكيد إن اليوزر الحالي عنده سجل طالب مربوط، وإنه صاحب الـ session دي
        $student = Auth::user()->student;
 
        if (! $student || $examSession->student_id !== $student->id) {
            return response()->json(['message' => 'غير مصرح لك بالدخول لهذه الجلسة'], 403);
        }
 
        if ($examSession->status !== 'active') {
            return response()->json(['message' => 'انتهت هذه الجلسة'], 422);
        }
 
        // نفس منطق التحقق من صلاحية الكود قبل المقارنة (lazy generation)
        $examSession->getOrRefreshCode();
 
        if (! $examSession->isCodeValid() || $examSession->current_code !== $request->code) {
            return response()->json(['message' => 'الكود غير صحيح أو منتهي الصلاحية'], 422);
        }
        
        // أول مرة يدخل الامتحان بيه بس
        if (! $examSession->started_at) {
            $examSession->started_at = now();
 
            if ($examSession->hasTimeLimit()) {
                $examSession->expires_at = now()->addMinutes($examSession->time_limit_minutes);
            }
 
            $examSession->save();
        }
        // return $examSession;
        return response()->json([
            'message' => 'تم التحقق من الكود بنجاح',
            'question_order' => $examSession->question_order,
            'choices_order' => $examSession->choices_order,
            'time_limit_minutes' => $examSession->time_limit_minutes,
            'expires_at' => $examSession->expires_at,
        ]);
    }
 
    /**
     * الطالب بيسلّم إجاباته، بيتحسب الدرجة تلقائي (MCQ فقط) وتتقفل الجلسة.
     *
     * الشكل المتوقع للـ body:
     * { "answers": { "12": 45, "5": 21, "8": null, ... } }  // question_id => selected_choice_id
     */
    public function submitExam(Request $request, ExamSession $examSession)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);
 
        $student = Auth::user()->student;
 
        if (! $student || $examSession->student_id !== $student->id) {
            return response()->json(['message' => 'غير مصرح لك بالتعامل مع هذه الجلسة'], 403);
        }
 
        if ($examSession->status !== 'active') {
            return response()->json(['message' => 'الجلسة دي مش نشطة أو اتسلمت بالفعل'], 422);
        }
 
        if ($examSession->submission) {
            return response()->json(['message' => 'تم تسليم هذا الامتحان بالفعل'], 409);
        }
 
        $submission = $this->gradeAndStoreSubmission($examSession, $request->input('answers', []));

        // امتحان تحديد المستوى: الطالب يحل بس ومايشوفش درجته خالص (الأدمن بس اللي بيشوفها)
        if ($examSession->exam->type === 'placement') {
            return response()->json([
                'message' => 'تم تسليم الامتحان بنجاح',
            ], 201);
        }

        return response()->json([
            'message' => 'تم تسليم الامتحان بنجاح',
            'score' => $submission->score,
            'total_points' => $submission->total_points,
        ], 201);
    }
 
    /**
     * منطق التصحيح والحفظ المشترك — بيستخدمه التسليم العادي (submitExam)
     * وكمان التسليم الإجباري لما ينتهي الوقت (auto-submit من take()).
     */
    protected function gradeAndStoreSubmission(ExamSession $examSession, array $answers): ExamSubmission
    {
        $exam = $examSession->exam;
        $student = $examSession->student;
        $questions = $exam->questions()->with('choices')->get()->keyBy('id');
 
        $totalPoints = 0;
        $score = 0;
        $answersToInsert = [];
 
        foreach ($questions as $questionId => $question) {
            $totalPoints += $question->points;
 
            $selectedChoiceId = $answers[$questionId] ?? null;
            $isCorrect = false;
            $pointsEarned = 0;
 
            if ($selectedChoiceId) {
                $choice = $question->choices->firstWhere('id', (int) $selectedChoiceId);
                if ($choice && $choice->is_correct) {
                    $isCorrect = true;
                    $pointsEarned = $question->points;
                    $score += $pointsEarned;
                }
            }
 
            $answersToInsert[] = [
                'question_id' => $questionId,
                'selected_choice_id' => $selectedChoiceId ?: null,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ];
        }
 
        $submittedAt = now();
        $durationSeconds = $examSession->started_at
            ? $examSession->started_at->diffInSeconds($submittedAt)
            : null;
 
        return DB::transaction(function () use ($examSession, $exam, $student, $score, $totalPoints, $answersToInsert, $submittedAt, $durationSeconds) {
            $submission = ExamSubmission::create([
                'exam_session_id' => $examSession->id,
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'score' => $score,
                'total_points' => $totalPoints,
                'submitted_at' => $submittedAt,
                'duration_seconds' => $durationSeconds,
            ]);
 
            foreach ($answersToInsert as $answer) {
                $answer['exam_submission_id'] = $submission->id;
                ExamAnswer::create($answer);
            }
 
            $examSession->update(['status' => 'ended']);
 
            return $submission;
        });
    }
 
    /**
     * صفحة الطالب: قائمة الامتحانات المفعّلة له (نشطة أو منتهية) — دي اللي
     * بتتفتح من خانة "امتحان" في السايد بار.
     */
    public function studentIndex()
    {
        $student = Auth::user()->student;
 
        if (! $student) {
            abort(403, 'حسابك مش مربوط بسجل طالب.');
        }
 
        $sessions = $student->examSessions()
            ->with('exam')
            ->latest()
            ->get();
        // return $sessions[0]->exam->type;
        return view('exam-sessions.index', compact('sessions'));
    }
 
    /**
     * صفحة الطالب: فورم إدخال الكود لجلسة معينة.
     */
    public function codeEntry(ExamSession $examSession)
    {
        $this->authorizeStudentSession($examSession);
 
        if ($examSession->status !== 'active') {
            return redirect()
                ->route('student.exam-sessions.index')
                ->with('warning', 'الجلسة دي مش نشطة.');
        }
 
        // لو دخل الكود صح قبل كده وبدأ الامتحان بالفعل، يوديه على صفحة الأسئلة مباشرة
        if ($examSession->started_at) {
            return redirect()->route('student.exam-sessions.take', $examSession);
        }
 
        return view('exam-sessions.enter-code', compact('examSession'));
    }
 
    /**
     * صفحة الطالب: عرض أسئلة الامتحان بترتيبها العشوائي وحل الامتحان.
     */
    public function take(ExamSession $examSession)
    {
        $this->authorizeStudentSession($examSession);
 
        if ($examSession->status !== 'active' || ! $examSession->started_at) {
            return redirect()
                ->route('student.exam-sessions.code-entry', $examSession)
                ->with('warning', 'لازم تدخل الكود الأول.');
        }
 
        if ($examSession->submission) {
            return redirect()
                ->route('student.exam-sessions.index')
                ->with('info', 'تم تسليم هذا الامتحان بالفعل.');
        }
 
        // لو الوقت انتهى (سواء الطالب قافل التاب أو أي سبب تاني) نسلّم تلقائي باللي موجود (فاضي هنا لأننا مش بنخزن إجابات جزئية سيرفر-سايد)
        if ($examSession->isTimeExpired()) {
            $this->gradeAndStoreSubmission($examSession, []);
 
            return redirect()
                ->route('student.exam-sessions.index')
                ->with('warning', 'انتهى وقت الامتحان وتم تسليمه تلقائيًا.');
        }
 
        $questions = $examSession->exam->questions()->with('choices')->get()->keyBy('id');
 
        // ترتيب الأسئلة والاختيارات حسب اللي اتولّد للطالب ده وقت التفعيل
        $orderedQuestions = collect($examSession->question_order)
            ->map(function ($questionId) use ($questions, $examSession) {
                $question = $questions->get($questionId);
                if (! $question) {
                    return null;
                }
 
                $choiceOrder = $examSession->choices_order[$questionId] ?? [];
                $question->orderedChoices = collect($choiceOrder)
                    ->map(fn ($choiceId) => $question->choices->firstWhere('id', $choiceId))
                    ->filter()
                    ->values();
 
                return $question;
            })
            ->filter()
            ->values();
 
        return view('exam-sessions.take', [
            'examSession' => $examSession,
            'questions' => $orderedQuestions,
            'remainingSeconds' => $examSession->remaining_seconds,
        ]);
    }
 
    /**
     * تأكيد إن اليوزر الحالي هو صاحب الجلسة دي فعلًا.
     */
    protected function authorizeStudentSession(ExamSession $examSession)
    {
        $student = Auth::user()->student;
 
        if (! $student || $examSession->student_id !== $student->id) {
            abort(403, 'غير مصرح لك بالوصول لهذه الجلسة.');
        }
    }

    /**
     * فحص خفيف لحالة الجلسة الحالية — بتستخدمه صفحة حل الامتحان بشكل دوري
     * عشان تعرف لو الأدمن أنهى الجلسة يدويًا وهي لسه مفتوحة عند الطالب.
     */
    public function sessionStatus(ExamSession $examSession)
    {
        $this->authorizeStudentSession($examSession);

        return response()->json([
            'status' => $examSession->status,
            'submitted' => (bool) $examSession->submission,
        ]);
    }
 
    /**
     * إنهاء الجلسة يدويًا من الأدمن (مثلاً في حالة انقطاع الطالب)
     */
    public function endSession(ExamSession $examSession)
    {
        $examSession->update(['status' => 'ended']);
 
        return response()->json(['message' => 'تم إنهاء الجلسة']);
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
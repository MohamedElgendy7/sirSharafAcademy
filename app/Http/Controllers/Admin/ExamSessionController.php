<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\ExamSubmission;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamSessionController extends Controller
{
    /**
     * صفحة الأدمن لإدارة تفعيل الامتحان لطلاب جروب معين:
     * تعرض كل طالب وحالته (لسه، نشط، منتهي) مع أزرار التفعيل/الإنهاء.
     */
    public function manage(Exam $exam, Group $group)
    {
        $students = $group->students()
            ->wherePivot('status', 'active')
            ->get();

        // كل الـ sessions الخاصة بالامتحان ده في الجروب ده، مفهرسة بمعرف الطالب
        $sessions = ExamSession::where('exam_id', $exam->id)
            ->where('group_id', $group->id)
            ->get()
            ->keyBy('student_id');

        return view('exam-sessions.manage', compact('exam', 'group', 'students', 'sessions'));
    }

    /**
     * تفعيل الامتحان لطالب واحد بس داخل جروب معين
     */
    public function activateForStudent(Request $request, Exam $exam, Group $group, Student $student)
    {
        // لو عنده session نشطة بالفعل لنفس الامتحان، منعملوش تكرار
        $existing = ExamSession::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'الطالب عنده بالفعل جلسة امتحان نشطة',
                'session' => $existing,
            ], 409);
        }

        $session = $this->createSessionFor($exam, $group, $student);

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
        // الطلاب النشطين في الجروب فقط (بفلترة pivot group_student.status = active)
        $students = $group->students()
            ->wherePivot('status', 'active')
            ->get();

        $created = [];
        $skipped = [];

        DB::transaction(function () use ($exam, $group, $students, &$created, &$skipped) {
            foreach ($students as $student) {
                $alreadyActive = ExamSession::where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'active')
                    ->exists();

                if ($alreadyActive) {
                    $skipped[] = $student->id;
                    continue;
                }

                $created[] = $this->createSessionFor($exam, $group, $student);
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
     * إنشاء exam_session جديدة لطالب مع ترتيب عشوائي مستقل للأسئلة والاختيارات
     */
    protected function createSessionFor(Exam $exam, Group $group, Student $student)
    {
        $questions = $exam->questions()->with('choices')->get();

        $questionOrder = $questions->pluck('id')->shuffle()->values()->all();

        $choicesOrder = [];
        foreach ($questions as $question) {
            $choicesOrder[$question->id] = $question->choices->pluck('id')->shuffle()->values()->all();
        }

        return ExamSession::create([
            'exam_id' => $exam->id,
            'group_id' => $group->id,
            'student_id' => $student->id,
            'activated_by' => Auth::id(),
            'activated_at' => now(),
            'status' => 'active',
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
            $examSession->save();
        }

        return response()->json([
            'message' => 'تم التحقق من الكود بنجاح',
            'question_order' => $examSession->question_order,
            'choices_order' => $examSession->choices_order,
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

        $exam = $examSession->exam;
        $questions = $exam->questions()->with('choices')->get()->keyBy('id');

        $totalPoints = 0;
        $score = 0;
        $answersToInsert = [];

        foreach ($questions as $questionId => $question) {
            $totalPoints += $question->points;

            $selectedChoiceId = $request->input("answers.$questionId");
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

        $submission = DB::transaction(function () use ($examSession, $exam, $student, $score, $totalPoints, $answersToInsert) {
            $submission = ExamSubmission::create([
                'exam_session_id' => $examSession->id,
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'score' => $score,
                'total_points' => $totalPoints,
                'submitted_at' => now(),
            ]);

            foreach ($answersToInsert as $answer) {
                $answer['exam_submission_id'] = $submission->id;
                ExamAnswer::create($answer);
            }

            $examSession->update(['status' => 'ended']);

            return $submission;
        });

        return response()->json([
            'message' => 'تم تسليم الامتحان بنجاح',
            'score' => $submission->score,
            'total_points' => $submission->total_points,
        ], 201);
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
     * إنهاء الجلسة يدويًا من الأدمن (مثلاً في حالة انقطاع الطالب)
     */
    public function endSession(ExamSession $examSession)
    {
        $examSession->update(['status' => 'ended']);

        return response()->json(['message' => 'تم إنهاء الجلسة']);
    }
}
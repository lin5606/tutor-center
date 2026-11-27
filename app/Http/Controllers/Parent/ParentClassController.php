<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Subject;
use App\Models\TutorClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Parent-owned class requests, their progress, and post-study tutor reviews. */
class ParentClassController extends Controller
{
    /** TODO: List only the authenticated parent's classes with status and tutor details. */
    public function index(Request $request): View
    {
        $this->assertParent($request);

        return view('parent.classes_index', [
            'classes' => TutorClass::where('parent_id', $request->user()->id)
                ->with(['subject', 'assignedTutor', 'schedules'])
                ->latest()->paginate(12),
        ]);
    }

    /** TODO: Render the new class form with the subject catalog and multiple schedule rows. */
    public function create(Request $request): View
    {
        $this->assertParent($request);

        return view('parent.class_form', ['subjects' => Subject::orderBy('name')->get()]);
    }

    /** TODO: Validate class and schedule inputs, then persist them atomically as pending_review. */
    public function store(Request $request): RedirectResponse
    {
        $this->assertParent($request);

        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:200'],
            'grade' => ['required', 'string', 'max:50'],
            'district' => ['required', 'string', 'max:100'],
            'private_address' => ['nullable', 'string', 'max:255'],
            'proposed_rate' => ['required', 'numeric', 'min:0.01'],
            'rate_unit' => ['required', 'in:per_session,per_month'],
            'requirement' => ['nullable', 'string'],
            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:2,8'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $schedules = $data['schedules'];
            unset($data['schedules']);
            $class = $request->user()->parentClasses()->create($data);
            $class->schedules()->createMany($schedules);
        });

        return redirect()->route('parent.classes.index')->with('success', 'Đã gửi yêu cầu duyệt lớp.');
    }

    /** TODO: Allow editing only a parent's class that has not been published or assigned. */
    public function edit(Request $request, TutorClass $class): View
    {
        $this->assertParent($request);
        abort_unless($class->parent_id === $request->user()->id && $class->status === 'pending_review', 403);

        return view('parent.class_form', [
            'class' => $class->load('schedules'),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    /** TODO: Update class fields and replace its schedule rows in one transaction. */
    public function update(Request $request, TutorClass $class): RedirectResponse
    {
        $this->assertParent($request);
        abort_unless($class->parent_id === $request->user()->id && $class->status === 'pending_review', 403);
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:200'],
            'grade' => ['required', 'string', 'max:50'],
            'district' => ['required', 'string', 'max:100'],
            'private_address' => ['nullable', 'string', 'max:255'],
            'proposed_rate' => ['required', 'numeric', 'min:0.01'],
            'rate_unit' => ['required', 'in:per_session,per_month'],
            'requirement' => ['nullable', 'string'],
            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:2,8'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
        ]);
        DB::transaction(function () use ($class, $data): void {
            $schedules = $data['schedules'];
            unset($data['schedules']);
            $class->update($data);
            $class->schedules()->delete();
            $class->schedules()->createMany($schedules);
        });

        return redirect()->route('parent.classes.index')->with('success', 'Đã cập nhật yêu cầu tìm gia sư.');
    }

    /** TODO: Let the owning parent cancel only a class allowed by center policy. */
    public function destroy(Request $request, TutorClass $class): RedirectResponse
    {
        $this->assertParent($request);
        abort_unless($class->parent_id === $request->user()->id, 403);

        abort_unless(in_array($class->status, ['pending_review', 'published'], true), 422, 'Lớp đã được giao; liên hệ trung tâm để xử lý hủy và hoàn phí.');
        $class->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy yêu cầu tìm gia sư.');
    }

    /** TODO: Verify ownership, completion, and assigned tutor before creating the unique review. */
    public function review(Request $request, TutorClass $class): View|RedirectResponse
    {
        $this->assertParent($request);
        abort_unless($class->parent_id === $request->user()->id, 403);
        abort_unless($class->status === 'completed' && $class->assigned_tutor_id, 403);

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'rating' => ['required', 'integer', 'between:1,5'],
                'comment' => ['nullable', 'string', 'max:5000'],
            ]);
            Review::firstOrCreate(
                ['class_id' => $class->id],
                [...$data, 'parent_id' => $request->user()->id, 'tutor_id' => $class->assigned_tutor_id]
            );

            return redirect()->route('parent.classes.index')->with('success', 'Đã gửi đánh giá.');
        }

        return view('parent.review_form', compact('class'));
    }

    /** TODO: Restrict the parent area to accounts carrying the parent role. */
    private function assertParent(Request $request): void
    {
        abort_unless($request->user()->roles()->where('slug', 'parent')->exists(), 403);
    }
}

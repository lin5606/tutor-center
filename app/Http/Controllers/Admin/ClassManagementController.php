<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\TutorClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Moderate parent requests, choose one tutor application, and create its fee record. */
class ClassManagementController extends Controller
{
    /** TODO: Show class requests and applications for administrator review. */
    public function index(): View
    {
        return view('admin.classes', [
            'classes' => TutorClass::with(['parent', 'subject', 'applications.tutor'])
                ->latest()->paginate(20),
        ]);
    }

    /** TODO: Publish only a pending request, making its public fields visible to tutors. */
    public function approve(TutorClass $class): RedirectResponse
    {
        abort_unless($class->status === 'pending_review', 422);
        $class->update(['status' => 'published', 'reviewed_by' => request()->user()->id, 'reviewed_at' => now()]);

        return back()->with('success', 'Đã duyệt và công khai yêu cầu tìm gia sư.');
    }

    /** TODO: Reject a pending request with an auditable reason. */
    public function reject(Request $request, TutorClass $class): RedirectResponse
    {
        $data = $request->validate(['reject_reason' => ['required', 'string', 'max:5000']]);
        abort_unless($class->status === 'pending_review', 422);
        $class->update([
            ...$data,
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu tìm gia sư.');
    }

    /** TODO: Atomically select one valid application, reject others, assign tutor, and create the fee. */
    public function assign(Request $request, TutorClass $class, Application $application): RedirectResponse
    {
        $data = $request->validate([
            'fee_amount' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::transaction(function () use ($request, $class, $application, $data): void {
            $lockedClass = TutorClass::whereKey($class->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedClass->status === 'published', 422);
            abort_unless($application->class_id === $lockedClass->id && $application->status === 'pending', 422);
            abort_unless(
                $application->tutor()->whereHas('tutorProfile', fn ($query) => $query->where('approval_status', 'approved'))->exists(),
                422,
                'Chỉ được giao lớp cho gia sư đã duyệt hồ sơ.'
            );

            $application->update([
                'status' => 'selected',
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
            ]);
            Application::where('class_id', $lockedClass->id)
                ->where('id', '<>', $application->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected', 'decided_by' => $request->user()->id, 'decided_at' => now()]);

            $lockedClass->update([
                'selected_application_id' => $application->id,
                'assigned_tutor_id' => $application->tutor_id,
                'status' => 'awaiting_tutor_payment',
            ]);
            Payment::create([
                'class_id' => $lockedClass->id,
                'application_id' => $application->id,
                'tutor_id' => $application->tutor_id,
                'amount' => $data['fee_amount'],
                'payment_method' => 'bank_transfer',
                'status' => 'pending',
            ]);
        });

        return back()->with('success', 'Đã giao lớp và tạo khoản phí nhận lớp.');
    }
}

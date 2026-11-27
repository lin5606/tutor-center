<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TutorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Review tutor credentials and record the administrator's decision. */
class TutorApprovalController extends Controller
{
    /** TODO: Show pending and previously reviewed tutor profiles. */
    public function index(): View
    {
        return view('admin.tutors', [
            'profiles' => TutorProfile::with(['user', 'subjects', 'reviewer'])
                ->latest()->paginate(20),
        ]);
    }

    /** TODO: Approve a pending profile and record reviewer/time. */
    public function approve(Request $request, TutorProfile $profile): RedirectResponse
    {
        abort_unless($profile->approval_status === 'pending', 422);
        $profile->update([
            'approval_status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'reject_reason' => null,
        ]);

        return back()->with('success', 'Đã duyệt hồ sơ gia sư.');
    }

    /** TODO: Require a rejection reason and record the reviewer/time. */
    public function reject(Request $request, TutorProfile $profile): RedirectResponse
    {
        $data = $request->validate(['reject_reason' => ['required', 'string', 'max:5000']]);
        abort_unless($profile->approval_status === 'pending', 422);
        $profile->update([
            ...$data,
            'approval_status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Đã từ chối hồ sơ gia sư.');
    }
}

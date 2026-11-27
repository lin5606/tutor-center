<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\TutorClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Tutor browsing/applications, assigned classes, fee evidence, and refund history. */
class TutorClassController extends Controller
{
    /** TODO: Show published class requests without selecting or displaying private_address. */
    public function index(Request $request): View
    {
        $this->assertTutor($request);

        return view('tutor.applications', [
            'classes' => TutorClass::where('status', 'published')
                ->select(['id', 'subject_id', 'title', 'grade', 'district', 'proposed_rate', 'rate_unit', 'created_at'])
                ->with('subject')->latest()->paginate(12),
        ]);
    }

    /** TODO: Require approved profile and prevent duplicate or late applications. */
    public function apply(Request $request, TutorClass $class): RedirectResponse
    {
        $this->assertTutor($request);
        abort_unless($class->status === 'published', 403);
        Application::firstOrCreate(
            ['class_id' => $class->id, 'tutor_id' => $request->user()->id],
            ['proposal_note' => $request->validate(['proposal_note' => ['nullable', 'string', 'max:5000']])['proposal_note'] ?? null]
        );

        return back()->with('success', 'Đã gửi đơn ứng tuyển.');
    }

    /** TODO: Show tutor applications and their decision statuses. */
    public function applications(Request $request): View
    {
        $this->assertTutor($request);

        return view('tutor.applications', [
            'applications' => Application::where('tutor_id', $request->user()->id)
                ->with('tutorClass.subject')->latest()->paginate(12),
        ]);
    }

    /** TODO: Expose private address only when this tutor's class is activated. */
    public function assigned(Request $request): View
    {
        $this->assertTutor($request);

        return view('tutor.payments', [
            'classes' => TutorClass::where('assigned_tutor_id', $request->user()->id)
                ->whereIn('status', ['active', 'in_progress', 'completed'])
                ->with(['subject', 'schedules', 'payments'])
                ->latest()->paginate(12),
        ]);
    }

    /** TODO: Accept payment reference/evidence only for the assigned tutor's pending fee. */
    public function submitPayment(Request $request, Payment $payment): RedirectResponse
    {
        $this->assertTutor($request);
        abort_unless($payment->tutor_id === $request->user()->id, 403);
        abort_unless(in_array($payment->status, ['pending', 'failed'], true), 403);
        $data = $request->validate([
            'transaction_code' => ['nullable', 'string', 'max:100'],
            'proof_image' => ['nullable', 'image', 'max:5120'],
        ]);
        if ($request->hasFile('proof_image')) {
            $data['proof_image'] = $request->file('proof_image')->store('payment-proofs', 'local');
        }
        $payment->update([...$data, 'status' => 'submitted']);

        return back()->with('success', 'Đã gửi minh chứng thanh toán để quản trị viên xác nhận.');
    }

    /** TODO: Display refund records belonging to the authenticated tutor. */
    public function refunds(Request $request): View
    {
        $this->assertTutor($request);

        return view('tutor.payments', [
            'refunds' => Refund::where('tutor_id', $request->user()->id)->with('payment')->latest()->paginate(12),
        ]);
    }

    /** TODO: Restrict tutor tools to accounts carrying the tutor role. */
    private function assertTutor(Request $request): void
    {
        abort_unless($request->user()->roles()->where('slug', 'tutor')->exists(), 403);
    }
}

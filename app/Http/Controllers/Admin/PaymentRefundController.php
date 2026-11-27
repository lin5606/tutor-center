<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\TutorClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Confirm fee receipts, create refund requests, and record refund outcomes. */
class PaymentRefundController extends Controller
{
    /** TODO: Show payment and refund queues with class, tutor, and status context. */
    public function index(): View
    {
        return view('admin.payments_refunds', [
            'payments' => Payment::with(['tutorClass', 'tutor'])->latest()->paginate(20, ['*'], 'payments_page'),
            'refunds' => Refund::with(['payment', 'tutorClass', 'tutor'])->latest()->paginate(20, ['*'], 'refunds_page'),
        ]);
    }

    /** TODO: Confirm a submitted payment and activate its matching assigned class atomically. */
    public function confirm(Request $request, Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($request, $payment): void {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedPayment->status === 'submitted', 422);
            $class = TutorClass::whereKey($lockedPayment->class_id)->lockForUpdate()->firstOrFail();
            abort_unless(
                $class->status === 'awaiting_tutor_payment' && $class->assigned_tutor_id === $lockedPayment->tutor_id,
                422
            );
            $lockedPayment->update([
                'status' => 'confirmed',
                'confirmed_by' => $request->user()->id,
                'confirmed_at' => now(),
            ]);
            $class->update(['status' => 'active']);
        });

        return back()->with('success', 'Đã xác nhận thanh toán và kích hoạt lớp.');
    }

    /** TODO: Reject submitted evidence and leave the class awaiting a valid payment. */
    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:5000']]);
        abort_unless($payment->status === 'submitted', 422);
        $payment->update([...$data, 'status' => 'failed']);

        return back()->with('success', 'Đã từ chối minh chứng thanh toán.');
    }

    /** TODO: Record a refund request only against confirmed payment and within unrefunded balance. */
    public function createRefund(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($payment, $data): void {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedPayment->status === 'confirmed', 422);
            $reservedRefunds = $lockedPayment->refunds()
                ->whereIn('status', ['pending', 'approved', 'completed'])
                ->sum('amount');
            abort_if(
                (float) $reservedRefunds + (float) $data['amount'] > (float) $lockedPayment->amount,
                422,
                'Số tiền hoàn vượt khoản đã thu.'
            );

            Refund::create([
                ...$data,
                'payment_id' => $lockedPayment->id,
                'class_id' => $lockedPayment->class_id,
                'tutor_id' => $lockedPayment->tutor_id,
                'status' => 'pending',
            ]);
        });

        return back()->with('success', 'Đã tạo yêu cầu hoàn phí.');
    }

    /** TODO: Approve/reject a refund or mark an approved refund completed with operator audit data. */
    public function processRefund(Request $request, Refund $refund): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:approved,completed,rejected']]);
        $allowed = match ($data['status']) {
            'approved', 'rejected' => $refund->status === 'pending',
            'completed' => $refund->status === 'approved',
        };
        abort_unless($allowed, 422, 'Trạng thái hoàn phí không hợp lệ.');
        $refund->update([
            'status' => $data['status'],
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Đã cập nhật trạng thái hoàn phí.');
    }
}

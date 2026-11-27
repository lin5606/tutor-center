<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\TutorClass;
use App\Models\TutorProfile;
use Illuminate\View\View;

/** Administrator dashboard counts and confirmed financial summaries. */
class DashboardController extends Controller
{
    /** TODO: Aggregate tutor/class statuses and confirmed payments less completed refunds. */
    public function index(): View
    {
        return view('admin.dashboard', [
            'tutorCount' => TutorProfile::count(),
            'classCounts' => TutorClass::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'totalPayments' => Payment::where('status', 'confirmed')->sum('amount'),
            'totalRefunds' => Refund::where('status', 'completed')->sum('amount'),
        ]);
    }
}

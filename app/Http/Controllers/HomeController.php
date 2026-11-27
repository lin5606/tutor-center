<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\TutorClass;
use App\Models\TutorProfile;
use Illuminate\View\View;

/** Public landing, published class listings, and approved tutor directory. */
class HomeController extends Controller
{
    /** TODO: Load a small public summary and return the landing page. */
    public function index(): View
    {
        return view('home.index', [
            'classes' => TutorClass::where('status', 'published')
                ->select(['id', 'subject_id', 'title', 'grade', 'district', 'proposed_rate', 'rate_unit', 'created_at'])
                ->latest()->take(6)->get(),
        ]);
    }

    /** TODO: Filter and paginate public class requests without selecting private_address. */
    public function classes(): View
    {
        return view('home.classes', [
            'classes' => TutorClass::query()
                ->where('status', 'published')
                ->select(['id', 'subject_id', 'title', 'grade', 'district', 'proposed_rate', 'rate_unit', 'created_at'])
                ->with('subject')
                ->latest()
                ->paginate(12),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    /** TODO: Show only a published class and never expose its private address publicly. */
    public function showClass(TutorClass $class): View
    {
        abort_unless($class->status === 'published', 404);
        $class = TutorClass::query()
            ->whereKey($class->id)
            ->where('status', 'published')
            ->select(['id', 'subject_id', 'title', 'grade', 'district', 'proposed_rate', 'rate_unit', 'requirement'])
            ->with(['subject', 'schedules'])
            ->firstOrFail();

        return view('home.class_detail', compact('class'));
    }

    /** TODO: List public tutor profiles only after administrator approval. */
    public function tutors(): View
    {
        return view('home.tutors', [
            'tutors' => TutorProfile::where('approval_status', 'approved')
                ->select(['id', 'user_id', 'university', 'major', 'bio', 'approval_status'])
                ->with(['user:id,full_name,avatar', 'subjects:id,name'])
                ->paginate(12),
        ]);
    }
}

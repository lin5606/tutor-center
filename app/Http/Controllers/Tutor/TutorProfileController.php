<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Tutor qualification profile, subject selection, and private document uploads. */
class TutorProfileController extends Controller
{
    /** TODO: Show the current tutor profile and subject selection form. */
    public function edit(Request $request): View
    {
        abort_unless($request->user()->roles()->where('slug', 'tutor')->exists(), 403);

        return view('tutor.profile', [
            'profile' => $request->user()->tutorProfile()->with('subjects')->firstOrCreate(),
            'subjects' => Subject::orderBy('name')->get(),
        ]);
    }

    /** TODO: Validate profile fields, store sensitive files privately, and sync selected subjects. */
    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->roles()->where('slug', 'tutor')->exists(), 403);

        $data = $request->validate([
            'university' => ['nullable', 'string', 'max:150'],
            'major' => ['nullable', 'string', 'max:100'],
            'graduation_year' => ['nullable', 'integer', 'between:1950,2100'],
            'bio' => ['nullable', 'string'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['integer', 'exists:subjects,id'],
            'id_card_front' => ['nullable', 'image', 'max:5120'],
            'id_card_back' => ['nullable', 'image', 'max:5120'],
            'student_card' => ['nullable', 'image', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $profile = $request->user()->tutorProfile()->firstOrCreate();
            $subjects = $data['subjects'] ?? [];
            unset($data['subjects']);

            foreach (['id_card_front', 'id_card_back', 'student_card'] as $document) {
                if ($request->hasFile($document)) {
                    $data[$document] = $request->file($document)->store('tutor-documents', 'local');
                }
            }

            $profile->fill($data)->save();
            $profile->subjects()->sync($subjects);
        });

        return back()->with('success', 'Đã cập nhật hồ sơ. Hồ sơ sẽ được quản trị viên xem xét.');
    }
}

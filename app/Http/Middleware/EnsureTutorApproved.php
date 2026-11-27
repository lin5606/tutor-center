<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Prevent unreviewed tutor accounts from applying for published classes. */
class EnsureTutorApproved
{
    /** TODO: Require an approved tutor profile before continuing to application routes. */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->tutorProfile?->approval_status === 'approved',
            403,
            'Gia sư cần được duyệt hồ sơ trước khi ứng tuyển.'
        );

        return $next($request);
    }
}

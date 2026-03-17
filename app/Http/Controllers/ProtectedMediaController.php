<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProtectedMediaController extends Controller
{
    public function streamVideo(Request $request, Lesson $lesson): Response
    {
        $user = $request->user();
        $course = $lesson->section?->course;

        abort_unless($course && $this->canAccess($user, $course), 403);
        abort_unless($lesson->content_url, 404);

        if ($this->isExternalUrl($lesson->content_url)) {
            return redirect($lesson->content_url);
        }

        if (Storage::disk('s3')->exists($lesson->content_url)) {
            $url = Storage::disk('s3')->temporaryUrl($lesson->content_url, now()->addMinutes(30));
            return redirect($url);
        }

        abort(404);
    }

    public function streamPdf(Request $request, Lesson $lesson): Response
    {
        $user = $request->user();
        $course = $lesson->section?->course;

        abort_unless($course && $this->canAccess($user, $course), 403);

        $media = $lesson->getFirstMedia('pdf');
        abort_unless($media, 404);

        return response()->file($media->getPath(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
        ]);
    }

    private function canAccess($user, $course): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->hasRole('admin') || $course->instructor_id === $user->id) {
            return true;
        }

        return $user->enrollments()
            ->where('course_id', $course->id)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();
    }

    private function isExternalUrl(string $url): bool
    {
        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }
}

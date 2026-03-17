<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with('course:id,title,slug')
            ->orderByDesc('issued_at')
            ->get();

        return response()->json([
            'data' => $certificates->map(fn ($c) => [
                'id' => $c->id,
                'uuid' => $c->uuid,
                'course' => [
                    'id' => $c->course->id,
                    'title' => $c->course->title,
                    'slug' => $c->course->slug,
                ],
                'issued_at' => $c->issued_at?->toISOString(),
                'verify_url' => $c->verify_url,
                'download_url' => route('certificates.download', $c->uuid),
            ]),
        ]);
    }

    public function verify(string $uuid): JsonResponse
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->with(['user:id,name', 'course:id,title'])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'uuid' => $certificate->uuid,
                'student_name' => $certificate->user->name,
                'course_title' => $certificate->course->title,
                'issued_at' => $certificate->issued_at?->toISOString(),
                'verified' => true,
            ],
        ]);
    }
}

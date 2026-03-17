<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function verify(string $uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->with(['user', 'course'])
            ->firstOrFail();

        $name = $this->maskName($certificate->user->name);

        return view('certificates.verify', [
            'certificate' => $certificate,
            'maskedName' => $name,
        ]);
    }

    public function index(Request $request)
    {
        $certificates = Certificate::where('user_id', $request->user()->id)
            ->with('course')
            ->orderByDesc('issued_at')
            ->get();

        return view('student.certificates', [
            'certificates' => $certificates,
        ]);
    }

    public function download(Request $request, string $uuid): StreamedResponse|\Illuminate\Http\Response
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->with(['user', 'course'])
            ->firstOrFail();

        Gate::authorize('view', $certificate);

        $path = 'certificates/' . $certificate->uuid . '.pdf';
        $filename = 'certificate-' . $certificate->course->slug . '-' . $certificate->uuid . '.pdf';

        $disk = Storage::disk('s3')->exists($path) ? 's3' : 'local';

        if ($disk === 's3') {
            $stream = Storage::disk('s3')->readStream($path);
            abort_unless(is_resource($stream), 404);

            return response()->streamDownload(function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            }, $filename, ['Content-Type' => 'application/pdf']);
        }

        if (Storage::disk('local')->exists($path)) {
            return response()->file(
                Storage::disk('local')->path($path),
                ['Content-Type' => 'application/pdf']
            );
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.default', [
            'studentName' => $certificate->user->name,
            'courseTitle' => $certificate->course->title,
            'instructorName' => $certificate->course->instructor?->name ?? 'Instructor',
            'completionDate' => $certificate->issued_at,
            'uuid' => $certificate->uuid,
            'siteName' => config('app.name', 'LearnFlow'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    protected function maskName(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return 'Student';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $first = $parts[0] ?? 'Student';
        $last = $parts[1] ?? null;

        if (! $last) {
            return $first;
        }

        return $first . ' ' . mb_substr($last, 0, 1) . '.';
    }
}


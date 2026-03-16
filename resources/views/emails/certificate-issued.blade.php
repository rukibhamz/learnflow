<x-mail::message>
# Congratulations, {{ $certificate->user->name }}!

Your **Certificate of Completion** for **{{ $certificate->course->title }}** is ready.

<x-mail::button :url="route('certificates.download', $certificate->uuid)">
Download Certificate (PDF)
</x-mail::button>

You can also verify and share your credential here:

`{{ route('certificates.verify', $certificate->uuid) }}`

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>


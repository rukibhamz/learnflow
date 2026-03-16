<x-mail::message>
# Welcome to {{ $enrollment->course->title }}!

Hi {{ $enrollment->user->name }},

You have successfully enrolled in **{{ $enrollment->course->title }}**. You're now ready to start learning!

<x-mail::button :url="route('learn.show', $enrollment->course->slug)">
Start Learning
</x-mail::button>

## Course Details

- **Instructor:** {{ $enrollment->course->instructor?->name }}
- **Level:** {{ ucfirst($enrollment->course->level?->value ?? 'All Levels') }}
- **Lessons:** {{ $enrollment->course->lessons()->count() }}

We're excited to have you on this learning journey. If you have any questions, feel free to reach out.

Happy learning!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Admin user for local dev
        $admin = User::firstOrCreate(
            ['email' => 'admin@learnflow.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Instructor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@learnflow.test'],
            [
                'name' => 'Jane Doe',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $instructor->assignRole('instructor');

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@learnflow.test'],
            [
                'name' => 'John Student',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $student->assignRole('student');

        // Create some courses
        $courses = [
            [
                'title' => 'Mastering User Experience & Visual Systems',
                'category' => 'Design',
                'price' => 89.00,
                'level' => 'intermediate',
                'description' => 'Learn the fundamental principles of UX/UI through real-world projects and case studies.',
            ],
            [
                'title' => 'Advanced React & Modern Web Architecture',
                'category' => 'Development',
                'price' => 129.00,
                'level' => 'advanced',
                'description' => 'Dive deep into React hooks, context API, and server-side rendering with Next.js.',
            ],
            [
                'title' => 'Growth Marketing for Scale-ups',
                'category' => 'Marketing',
                'price' => 75.00,
                'level' => 'beginner',
                'description' => 'Master the art of customer acquisition, retention, and funnel optimization.',
            ],
        ];

        foreach ($courses as $c) {
            $course = Course::create([
                'instructor_id' => $instructor->id,
                'title' => $c['title'],
                'slug' => Str::slug($c['title']),
                'description' => $c['description'],
                'short_description' => $c['description'],
                'price' => $c['price'],
                'level' => $c['level'],
                'status' => 'published',
            ]);

            // Create a section
            $section = Section::create([
                'course_id' => $course->id,
                'title' => 'Introduction',
                'order' => 1,
            ]);

            // Create a lesson
            Lesson::create([
                'section_id' => $section->id,
                'title' => 'Welcome to the course',
                'type' => 'video',
                'order' => 1,
                'duration_seconds' => 300,
            ]);
        }
    }
}

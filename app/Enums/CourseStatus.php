<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Review = 'review';
    case Published = 'published';
    case Rejected = 'rejected';
    case Archived = 'archived';
}

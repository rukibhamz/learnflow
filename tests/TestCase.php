<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $installed = storage_path('framework/installed');
        if (! file_exists($installed)) {
            @touch($installed);
        }
    }
}

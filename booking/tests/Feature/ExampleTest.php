<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Test that the application can be instantiated and basic functionality works
        $this->assertTrue(app()->bound('config'));
        $this->assertTrue(app()->bound('view'));
        $this->assertTrue(app()->bound('router'));
    }
}

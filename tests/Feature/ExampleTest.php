<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_login_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_the_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
}

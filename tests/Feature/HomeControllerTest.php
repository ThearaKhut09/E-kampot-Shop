<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── Home Page ────────────────────────────────────────────────────────────

    /** @test */
    public function home_page_returns_200(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    // ─── About Page ───────────────────────────────────────────────────────────

    /** @test */
    public function about_page_returns_200(): void
    {
        $response = $this->get(route('about'));

        $response->assertStatus(200);
    }

    // ─── Contact Page ─────────────────────────────────────────────────────────

    /** @test */
    public function contact_page_returns_200(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
    }

    /** @test */
    public function contact_form_can_be_submitted_with_valid_data(): void
    {
        $response = $this->post(route('contact.store'), [
            'name'    => 'Sophea Chan',
            'email'   => 'sophea@example.com',
            'subject' => 'Question about Kampot Pepper',
            'message' => 'I would like to know more about your products.',
        ]);

        // Should redirect back with success or return 200
        $this->assertContains($response->status(), [200, 302]);
    }

    /** @test */
    public function contact_form_requires_name_email_and_message(): void
    {
        $response = $this->post(route('contact.store'), []);

        // Should fail validation
        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function contact_form_requires_valid_email(): void
    {
        $response = $this->post(route('contact.store'), [
            'name'    => 'Test User',
            'email'   => 'not-an-email',
            'message' => 'Test message content.',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\TutorClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** TODO: Public class listings and details must never expose the private street address. */
    public function test_public_class_pages_do_not_expose_private_address(): void
    {
        $parent = User::create([
            'full_name' => 'Parent Test',
            'email' => 'parent@example.test',
            'password' => 'Password123!',
            'phone' => '0901111111',
        ]);
        $subject = Subject::create(['name' => 'Mathematics']);
        $class = TutorClass::create([
            'parent_id' => $parent->id,
            'subject_id' => $subject->id,
            'title' => 'Math tutor required',
            'grade' => 'Grade 9',
            'district' => 'District 1',
            'private_address' => '42 Private Street',
            'proposed_rate' => 200000,
            'rate_unit' => 'per_session',
            'status' => 'published',
        ]);

        $this->get(route('classes.show', $class))
            ->assertOk()
            ->assertDontSee('42 Private Street');
        $this->get(route('classes.index'))
            ->assertOk()
            ->assertDontSee('42 Private Street');
    }
}

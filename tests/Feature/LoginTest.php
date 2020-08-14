<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_should_allow_users_to_log_in_with_correct_credentials()
    {
        $user = factory(User::class)->create(['type'=>User::TYPE_EMPLOYEE]);

        $response = $this->post(
            route('login'),
            [
                'email' => $user->email,
                'password' => 'password',
            ],
        );

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }
    
    /**
     * @test
     */
    public function it_should_redirect_admin_to_users_page()
    {
        $user = factory(User::class)->create(['type'=>User::TYPE_ADMIN]);

        $response = $this->post(
            route('login'),
            [
                'email' => $user->email,
                'password' => 'password',
            ],
        );

        $response->assertRedirect(route('users'));
    }

    /**
     * @test
     */
    public function it_should_reject_users_from_log_in_with_invalid_credentials()
    {
        $user = factory(User::class)->create();

        $response = $this->post(
            route('login'),
            [
                'email' => $user->email,
                'password' => 'PASS',
            ],
        );

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
}

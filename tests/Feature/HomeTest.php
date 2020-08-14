<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Description of HomeTest
 *
 * @author G Brabyn
 */
class HomeTest extends TestCase
{
    use WithoutMiddleware;
    
    /**
     * @test
     */
    public function home_should__load()
    {
        $response = $this->get('/home');

        $response->assertStatus(200);
        $response->assertSee('You are logged in!');
        $response->assertViewIs('home');
    }
}

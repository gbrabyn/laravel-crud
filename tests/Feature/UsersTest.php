<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;

class UsersTest extends TestCase
{
    use DatabaseTransactions;

    public function testIndexShouldLoad()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('users'));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    public function testUnauthenticatedUserCannotSeeIndex()
    {
        $response = $this->get(route('users'));

        $response->assertRedirect('/login');
    }

    public function testAuthenticatedUserCanSeeUsers()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create(['name' => '11111111', 'email' => '11111111@aaa.de']);

        $response = $this->actingAs($user1)
            ->get(route('users'));

        $response->assertSee($user2->email);
    }

    public function testCanSearchUsersByEmail()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create(['name' => '11111111', 'email' => '11111111@aaa.de']);

        $response = $this->actingAs($user)
            ->get(route('users', ['nameOrEmail' => $user->email]));

        $response->assertSee($user->email);
        $response->assertDontSee($user2->email);
    }

    public function testCanSearchUsersByName()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create(['name' => '11111111', 'email' => '11111111@aaa.de']);

        $response = $this->actingAs($user)
            ->get(route('users', ['nameOrEmail' => $user->name]));

        $response->assertSee($user->email);
        $response->assertDontSee($user2->email);
    }

    public function testCanSearchUsersByType()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create(['name' => '11111111', 'email' => '11111111@aaa.de', 'type' => User::TYPE_EMPLOYEE]);

        $response = $this->actingAs($user)
            ->get(route('users', ['type' => User::TYPE_ADMIN]));

        $response->assertSee('admin');
        $response->assertDontSee($user2->email);
    }

    public function testCanSearchUsersByOrganisation()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create([
            'name' => '11111111',
            'email' => '11111111@aaa.de',
            'organisation_id' => factory(Organisation::class)->create()->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('users', ['organisation' => $user->organisation_id]));

        $response->assertSee($user->organisation->name);
        $response->assertSee($user->email);
        $response->assertDontSee($user2->email);
    }

    public function testAdminUserCanViewCreatePage()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->get(route('users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertSee('Add User');
    }

    public function testEmployeesCannotViewCreatePage()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);

        $response = $this->actingAs($user)
            ->get(route('users.create'));

        $response->assertStatus(403);
    }

    public function testEmployeesCannotStoreNewUser()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $newUser = factory(User::class)->make();

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertStatus(403);
    }

    public function testAdminCanStoreNewUser()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make();

        $this->assertDatabaseMissing('users', ['email' => $newUser->email]);

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('users');
        $this->assertDatabaseHas('users', ['email' => $newUser->email]);
    }

    public function testNewUserNameRequired()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make();
        $newUser->name = null;

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasErrors('name');
    }

    public function testNewUserEmailRequired()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make();
        $newUser->email = null;

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasErrors('email');
    }

    public function testNewUserUniqueEmailRequired()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make(['email' => $user->email]);

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasErrors('email');
    }

    public function testNewUserMustHaveValidType()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make(['type' => 'xyz']);

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasErrors('type');
    }

    public function testNewUserOrganisationOptionalIfAdmin()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make(['type' => User::TYPE_ADMIN, 'organisation_id' => null]);

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => $newUser->email]);
    }

    public function testNewUserOrganisationRequiredIfEmployee()
    {
        $newUser = factory(User::class)->make(['type' => User::TYPE_EMPLOYEE, 'organisation_id' => null]);
        $adminUser = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($adminUser)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasErrors('organisation_id');
    }

    public function testNewUserValidOrganisationId()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $newUser = factory(User::class)->make(['organisation_id' => 9999999999]);

        $response = $this->actingAs($user)
            ->post(route('users.store'), $newUser->toArray());

        $response->assertSessionHasErrors('organisation_id');
    }

    public function testEmployeeCannotViewEdit()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $editUser = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('users.edit', $editUser));

        $response->assertStatus(403);
    }

    public function testAdminCanViewEmployeeEdit()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $editUser = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);

        $response = $this->actingAs($user)
            ->get(route('users.edit', $editUser));

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertSee('Edit User');
        $response->assertSee($editUser->email);
        $response->assertSee($editUser->name);
    }

    public function testAdminCannotViewEditOfOtherAdmin()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $adminUser = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->get(route('users.edit', $adminUser));

        $response->assertStatus(403);
    }

    public function testAdminCanViewEditofSelf()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->get(route('users.edit', $user));

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertSee('Edit User');
        $response->assertSee($user->email);
    }

    public function testEmployeeCannotUpdate()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $editUser = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->put(route('users.update', $editUser), $editUser->toArray());

        $response->assertStatus(403);
    }

    public function testAdminCanUpdateEmployee()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $editUser = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);

        $newName = $editUser->name . 'ZZZ';
        $editUser->name = $newName;

        $response = $this->actingAs($user)
            ->put(route('users.update', $editUser), $editUser->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('users');
        $this->assertDatabaseHas('users', ['id' => $editUser->id, 'name' => $newName]);
    }

    public function testtAdminCannotUpdateOtherAdmin()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $adminUser = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->put(route('users.update', $adminUser), $adminUser->toArray());

        $response->assertStatus(403);
    }

    public function testAdminCanUpdateSelf()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $newName = $user->name . 'ZZZ';
        $user->name = $newName;

        $response = $this->actingAs($user)
            ->put(route('users.update', $user), $user->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('users');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => $newName]);
    }

    public function testUpdateRequiresUniqueEmail()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $editUser = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $editUser->email = $user->email;

        $response = $this->actingAs($user)
            ->put(route('users.update', $editUser), $editUser->toArray());

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['id' => $editUser->id, 'email' => $user->email]);
    }

    public function testEmployeeCannotDelete()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $expiredUser = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->delete(route('users.delete', $expiredUser));

        $response->assertStatus(403);
    }

    public function testAdminCanDeleteEmployee()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $expiredUser = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $this->assertDatabaseHas('users', ['id' => $expiredUser->id]);

        $response = $this->actingAs($user)
            ->delete(route('users.delete', $expiredUser));

        $response->assertStatus(200);
        $response->assertJson(['userId' => $expiredUser->id, 'success' => true], false);
        $this->assertDatabaseMissing('users', ['id' => $expiredUser->id]);
    }

    public function testAdminCannotDeleteSelf()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $response = $this->actingAs($user)
            ->delete(route('users.delete', $user));

        $response->assertStatus(403);
    }

    public function testAdminCannotDeleteOtherAdmin()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $expiredUser = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->delete(route('users.delete', $user));

        $response->assertStatus(403);
    }
}

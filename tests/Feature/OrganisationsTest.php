<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Testing\TestResponse;
use App\Models\User;
use App\Models\Organisation;

class OrganisationsTest extends TestCase
{
    use DatabaseTransactions;

    public function testUnauthenticatedUserCannotSeeIndex()
    {
        $response = $this->get(route('organisations'));

        $response->assertRedirect('/login');
    }

    public function testEmployeesCannotSeeIndex()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);

        $response = $this->actingAs($user)
            ->get(route('organisations'));

        $response->assertStatus(403);
    }

    public function testAdminCanSeeIndex(): TestResponse
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->create(['name' => 'AAA1']);

        $response = $this->actingAs($user)
            ->get(route('organisations'));

        $response->assertStatus(200);
        $response->assertViewIs('organisations.index');

        return $response;
    }

    /** @depends testAdminCanSeeIndex */
    public function testIndexListsOrganisations(TestResponse $response)
    {
        $response->assertSee('AAA1');
    }

    public function testEmployeesCannotSeeCreate()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);

        $response = $this->actingAs($user)
            ->get(route('organisations.create'));

        $response->assertStatus(403);
    }

    public function testAdminCanSeeCreate()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->get(route('organisations.create'));

        $response->assertStatus(200);
        $response->assertViewIs('organisations.edit');
    }

    public function testAdminCanStoreNewOrganisation()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->make();

        $this->assertDatabaseMissing('organisation', ['name' => $organisation->name]);

        $response = $this->actingAs($user)
            ->post(route('organisations.store', $organisation->toArray()));

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('organisations');
        $this->assertDatabaseHas('organisation', ['name' => $organisation->name]);
    }

    public function testEmployeesCannotStore()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $organisation = factory(Organisation::class)->make();

        $response = $this->actingAs($user)
            ->post(route('organisations.store', $organisation->toArray()));

        $response->assertStatus(403);
    }

    public function testStoreRequiresName(): Organisation
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->make(['name' => null]);

        $this->assertDatabaseMissing('organisation', ['name' => $organisation->name]);

        $response = $this->actingAs($user)
            ->post(route('organisations.store', $organisation->toArray()));

        $response->assertSessionHasErrors('name');

        return $organisation;
    }

    /** @depends testStoreRequiresName */
    public function testStoreRequiresUniqueName(Organisation $organisation)
    {
        $organisation->name = 'AAA1';
        factory(Organisation::class)->create(['name' => $organisation->name]);
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->post(route('organisations.store', $organisation->toArray()));

        $response->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    public function testEmployeesCannotSeeEdit()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $organisation = factory(Organisation::class)->create();

        $response = $this->actingAs($user)
            ->get(route('organisations.edit', $organisation));

        $response->assertStatus(403);
    }

    public function testAdminCanSeeEdit()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->create();

        $response = $this->actingAs($user)
            ->get(route('organisations.edit', $organisation));

        $response->assertStatus(200);
        $response->assertViewIs('organisations.edit');
    }

    public function testEmployeesCannotUpdate()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $organisation = factory(Organisation::class)->create();

        $response = $this->actingAs($user)
            ->put(route('organisations.update', $organisation), $organisation->toArray());

        $response->assertStatus(403);
    }

    public function testAdminCanUpdate()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->create();
        $newName = $organisation->name . 'ZZZZ';
        $organisation->name = $newName;

        $response = $this->actingAs($user)
            ->put(route('organisations.update', $organisation), $organisation->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('organisations');
        $this->assertDatabaseHas('organisation', ['id' => $organisation->id, 'name' => $newName]);
    }

    public function testUpdateRequiresName()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->create();
        $organisation->name = '';

        $response = $this->actingAs($user)
            ->put(route('organisations.update', $organisation), $organisation->toArray());

        $response->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    public function testAdminCanDelete()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);
        $organisation = factory(Organisation::class)->create();
        $organisationKeep = factory(Organisation::class)->create();
        $organisationUser = factory(User::class)->create([
            'type' => User::TYPE_EMPLOYEE,
            'organisation_id' => $organisation->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('organisations.delete'), ['id' => $organisation->id]);

        $response->assertRedirect('organisations');
        $this->assertDatabaseMissing('organisation', ['id' => $organisation->id]);
        $this->assertDatabaseMissing('users', ['organisation_id' => $organisation->id]);
        $this->assertDatabaseHas('organisation', ['id' => $organisationKeep->id]);
    }

    public function testDeleteNonExistentReturns404()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_ADMIN]);

        $response = $this->actingAs($user)
            ->delete(route('organisations.delete'), ['id' => 9999999999]);

        $response->assertStatus(404);
    }

    public function testEmployeesCannotDelete()
    {
        $user = factory(User::class)->create(['type' => User::TYPE_EMPLOYEE]);
        $organisation = factory(Organisation::class)->create();

        $response = $this->actingAs($user)
            ->delete(route('organisations.delete'), ['id' => $organisation->id]);

        $response->assertStatus(403);
    }
}

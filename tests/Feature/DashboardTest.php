<?php

use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('panel'))->assertRedirect(route('login'));
});

test('authenticated admin can visit the panel', function () {
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['nombre' => Role::ADMIN], ['descripcion' => 'Admin']);
    $user->roles()->attach($role);

    $this->actingAs($user)->get(route('panel'))->assertOk();
});

test('shared auth includes user role label', function () {
    $user = User::factory()->create(['role' => Role::CAJERA]);
    $role = Role::firstOrCreate(['nombre' => Role::CAJERA], ['descripcion' => 'Cajera']);
    $user->roles()->attach($role);

    $this->actingAs($user)
        ->get(route('panel'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('auth.etiqueta_rol', 'Cajera'));
});

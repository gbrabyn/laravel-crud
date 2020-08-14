<?php

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Home', route('home'));
});

Breadcrumbs::for('users', function ($trail) {
    $trail->parent('home');
    $trail->push('Users', route('users'));
});

Breadcrumbs::for('users.create', function ($trail) {
    $trail->parent('users');
    $trail->push('Create', route('users.create'));
});

Breadcrumbs::for('users.edit', function ($trail, $user) {
    $trail->parent('users');
    $trail->push('Edit', route('users.edit', $user));
});






Breadcrumbs::for('organisations', function ($trail) {
    $trail->parent('home');
    $trail->push('Organisations', route('organisations'));
});

Breadcrumbs::for('organisations.create', function ($trail) {
    $trail->parent('organisations');
    $trail->push('Create', route('organisations.create'));
});

Breadcrumbs::for('organisations.edit', function ($trail, $organisation) {
    $trail->parent('organisations');
    $trail->push('Edit', route('organisations.edit', $organisation));
});
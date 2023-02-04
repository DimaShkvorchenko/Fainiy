<?php

const ADMIN_PAYLOAD = [
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john.smith@test.com',
    'password' => '123456',
    'password_confirmation' => '123456',
];

const CLIENT_PAYLOAD = [
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john.smith.client@test.com',
    'password' => '123456',
    'password_confirmation' => '123456',
];

const STAFF_PAYLOAD = [
    'first_name' => 'Ivan',
    'last_name' => 'Bunin',
    'email' => 'ivan.bunin.staff@test.com',
    'password' => '123456',
    'password_confirmation' => '123456',
];

const ADMIN_CREDENTIALS = [
    'email' => 'john.smith@test.com',
    'password' => '123456',
];

dataset('missing_users', ['S', 0, -1, 100000]);

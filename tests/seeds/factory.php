<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory = app(Factory::class);

$factory->define(Tests\Models\User::class, function (Faker $faker) {
    return [
        'username' => $faker->userName,
        'email'    => $faker->email,
        'mobile'   => $faker->phoneNumber,
        'avatar'   => $faker->imageUrl(),
        'password' => bcrypt('123456'),
    ];
});


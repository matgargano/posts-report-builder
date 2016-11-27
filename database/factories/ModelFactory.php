<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Cafemedia\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Cafemedia\Post::class, function (Faker\Generator $faker) {

    return [
        'id' => $faker->unique()->numberBetween(1,1000),
        'title' => $faker->sentence(),
        'privacy' => $faker->randomElement(['public','private']),
        'likes' => $faker->numberBetween(1,1000),
        'views' => $faker->numberBetween(1,1000),
        'comments' => $faker->numberBetween(1,1000),
        'timestamp' => $faker->date('D M d h:i:s Y', $faker->unixTime)

    ];
});
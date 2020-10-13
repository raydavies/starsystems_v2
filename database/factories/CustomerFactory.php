<?php

use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'street_address' => $faker->streetAddress,
		'city' => $faker->city,
        'state_province' => $faker->stateAbbr,
        'zip_code' => $faker->postcode,
        'email' => $faker->unique()->email,
        'phone_home' => $faker->phoneNumber,
        'phone_work' => $faker->optional(0.1)->phoneNumber,
        'child_name' => $faker->optional()->firstName,
        'grade' => $faker->optional()->numberBetween(0, 12),
        'created_at' => $faker->dateTimeBetween('-1 year')
    ];
});

<?php

use App\Models\Testimonial;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Testimonial::class, function (Faker $faker) {
	return [
		'name' => $faker->name,
		'city' => $faker->city,
		'state_province' => $faker->stateAbbr,
		'comment' => $faker->realText,
		'flag_active' => 1,
		'sort' => $faker->unique()->randomDigit,
	];
});

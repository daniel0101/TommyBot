<?php

use Faker\Generator as Faker;

$factory->define(App\Offer::class, function (Faker $faker) {
    return [
        'name'=>$faker->sentence(),
        'image'=>$faker->imageUrl(),
        'description'=>$faker->paragraph(),
        'url'=>'https://www.234bet.com',
        'offer_date'=>date("Y-m-d", strtotime("+1 week")),
    ];
});

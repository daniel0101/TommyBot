<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Offer::class, function (Faker $faker) {
    return [
        'name'=>$faker->sentence(),
        'image'=>$faker->imageUrl(),
        'description'=>$faker->paragraph(),
        'url'=>'https://www.234bet.com',
        'offer_date'=>Carbon::today('WAT'),
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(App\UserProfile::class, function (Faker $faker) {
    return [
        'bio'=>$faker->paragraph, //pongo solo bio porque el resto de los campos son opcionales
    ];
});

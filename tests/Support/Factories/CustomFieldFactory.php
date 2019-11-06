<?php

use Faker\Generator;
use Givebutter\LaravelCustomFields\Models\CustomField;

$factory->define(CustomField::class, function (Generator $faker) {
    return [
        'type' => $faker->randomElement([
            'text', 'textarea', 'radio', 'select', 'checkbox', 'number',
        ]),
        'required' => $faker->boolean,
        'answers' => $faker->words(rand(1, 4)),
        'title' => $faker->word,
        'description' => $faker->sentence,
        'order' => $faker->numberBetween(1, 9),
    ];
});

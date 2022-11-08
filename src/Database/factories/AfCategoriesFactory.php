<?php

namespace East\LaravelActivityfeed\Database\factories;

use East\LaravelActivityfeed\af_categories;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AfCategoriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AfCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}

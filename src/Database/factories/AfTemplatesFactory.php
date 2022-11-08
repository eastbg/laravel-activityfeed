<?php

namespace East\LaravelActivityfeed\Database\factories;

use East\LaravelActivityfeed\af_templates;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class AfTemplatesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AfTemplate::class;

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

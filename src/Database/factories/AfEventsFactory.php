<?php

namespace East\LaravelActivityfeed\Database\factories;

use East\LaravelActivityfeed\af_events;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class AfEventsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AfEvent::class;

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

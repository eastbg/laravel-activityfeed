<?php

namespace East\LaravelActivityfeed\Actions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfTriggerActions extends Model
{
    use HasFactory;

    private $triggers;
    private $schema;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    //public function



}

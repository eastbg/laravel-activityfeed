<?php

namespace East\LaravelActivityfeed\Http\Api;

use East\LaravelActivityfeed\Facades\AfHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AfData extends Controller
{


    public function columns(Request $request)
    {
        return AfHelper::getColumns($request->get('table_name'));
    }

    public function targeting(Request $request)
    {
        return AfHelper::getTargeting($request->get('table_name'));
    }
}

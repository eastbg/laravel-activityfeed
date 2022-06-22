<?php

namespace East\LaravelActivityfeed\Http\Api;

use East\LaravelActivityfeed\Facades\AfHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AfData extends Controller
{


    public function index(Request $request)
    {
        return AfHelper::getColumns($request->get('table'));
        $results = ['yksi' => 'yksi','kaksi'=>'kaksi'];
        return $request;
    }
}

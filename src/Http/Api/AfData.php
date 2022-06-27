<?php

namespace East\LaravelActivityfeed\Http\Api;

use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Facades\AfRender;
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
        return AfHelper::getTableTargeting($request->get('table_name'));
    }

    public function tables(Request $request)
    {
        return AfHelper::getTables();
    }

    public function relationships(Request $request)
    {
        return AfHelper::getRelationships($request->get('table_name'));
    }

    public function varReplacer(Request $request)
    {
        return AfRender::mockVarReplacer(
            $request->get('data'),
            $request->get('id'),
            $request->get('template')
        );
    }

    public function tableInfo(Request $request)
    {
        return [
            'relations' => AfHelper::getRelationships($request->get('table_name')),
            'fields' => AfHelper::getTableFields($request->get('table_name'))
        ];
    }
}

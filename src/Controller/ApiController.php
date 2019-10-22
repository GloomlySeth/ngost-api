<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    const LIMIT = 15;
    const OFFSET = 1;
    const SORT_FIELD = 'created_at';
    const SORT_DIRECTION = 'DESC';


    public function sorting (Request $request) {
        $sort_field = $request->get('sort_field')?$request->get('sort_field'):self::SORT_FIELD;
        $sort_direction = $request->get('sort_field')?$request->get('sort_direction'):self::SORT_DIRECTION;
        return [$sort_field => $sort_direction];
    }

    public function getLimit (Request $request) {
        $limit = $request->get('limit')?$request->get('limit'):self::LIMIT;
        return $limit;
    }

    public function getOffset (Request $request) {
        $offset = $request->get('offset')?$request->get('offset'):self::OFFSET;
        return $offset;
    }

}

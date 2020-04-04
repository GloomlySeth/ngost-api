<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    const LIMIT = null;
    const OFFSET = 0;
    const SORT_FIELD = 'created_at';
    const SORT_DIRECTION = 'DESC';


    public function sorting (Request $request) {
        $sort_field = $request->get('sort_field')?$request->get('sort_field'):self::SORT_FIELD;
        $sort_direction = $request->get('sort_field')?$request->get('sort_direction'):self::SORT_DIRECTION;
        return [$sort_field => $sort_direction];
    }

    public function getLimit (Request $request) {
        return $request->get('limit')?$request->get('limit'):self::LIMIT;
    }

    public function getOffset (Request $request) {
        return $request->get('offset')?$request->get('offset'):self::OFFSET;
    }

    /**
     * @Route("/uploads/files/{user}/{name}")
     * @param $user
     * @param $name
     * @return BinaryFileResponse
     */
    public function downloadAction($user, $name)
    {
        $path ="/uploads/files/".$user."/";
        $file = $path.$name; // Path to the file on the server
        $response = new BinaryFileResponse($file);

        // Give the file a name:
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$name);

        return $response;
    }

}

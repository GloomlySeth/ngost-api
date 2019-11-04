<?php

namespace App\Controller;

use App\Service\MediaHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class MediaController
 * @package App\Controller
 * @Route("/api")
 */

class MediaController extends AbstractController
{
    /**
     * @Route("/media", name="media", methods={"POST"})
     * @param Request $request
     * @param MediaHelper $helper
     * @return JsonResponse
     */
    public function createFile(Request $request, MediaHelper $helper)
    {
        $file = $request->get('file');
        $file = $helper->upload($file);
        return new JsonResponse([
            'message' => 'add new file',
            'id' => $file
        ],201);
    }
}

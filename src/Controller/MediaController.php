<?php

namespace App\Controller;

use App\Entity\Media;
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
     * @Route("/media", methods={"POST"})
     * @param Request $request
     * @param MediaHelper $helper
     * @return JsonResponse
     */
    public function createFile(Request $request, MediaHelper $helper)
    {
        $file = $request->files->get('file');

        $file = $helper->upload($file);
        return new JsonResponse([
            'message' => 'Add new file',
            'id' => $file->getId(),
            'path' => $file->getFilePath()
        ],201);
    }

    /**
     * @Route("/media/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getFile($id)
    {
        $file = $this->getDoctrine()->getRepository(Media::class)->find($id);
        if (is_null($file)) {
            return new JsonResponse([
                'message' => 'File not exist'
            ],200);
        }
        return new JsonResponse([
            'id' => $file->getId(),
            'path' => $file->getFilePath()
        ],200);
    }
}

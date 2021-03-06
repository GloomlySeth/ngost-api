<?php

namespace App\Controller;

use App\Entity\Files;
use App\Entity\Media;
use App\Entity\Users;
use App\Service\MediaHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class MediaController
 * @package App\Controller
 * @Route("/api")
 */
class MediaController extends ApiController
{


    /**
     * @Route("/media", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllMedia(Request $request)
    {
        $media = $this->getDoctrine()->getRepository(Media::class)->findBy(
            ['deleted_at' => null],
            $this->sorting($request),
            $this->getLimit($request),
            $this->getOffset($request)
        );

        if (is_null($media)) {
            return new JsonResponse([
                'message' => 'No data'
            ]);
        }
        $data = [];
        foreach ($media as $file) {
            $data[] = [
                'id' => $file->getId(),
                'path' => $file->getFilePath()
            ];
        }

        return new JsonResponse([
            'response' => $data
        ]);
    }

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
        ], 201);
    }

    /**
     * @Route("/media/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getFileInfo($id)
    {
        $file = $this->getDoctrine()->getRepository(Media::class)->find($id);
        if (is_null($file)) {
            return new JsonResponse([
                'message' => 'File exist'
            ], 200);
        }
        return new JsonResponse([
            'id' => $file->getId(),
            'path' => $file->getFilePath()
        ], 200);
    }


    /**
     * @Route("/files", methods={"POST"})
     * @param Request $request
     * @param MediaHelper $helper
     * @return JsonResponse
     */
    public function create(Request $request, MediaHelper $helper)
    {
        $file = $request->files->get('file');

        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());
        $file = $helper->uploadFile($file, $user);
        return new JsonResponse([
            'message' => 'Add new file',
            'id' => $file->getId(),
            'path' => $file->getPath()
        ], 201);
    }

    /**
     * @Route("/files/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getFile($id)
    {
        $file = $this->getDoctrine()->getRepository(Files::class)->find($id);
        if (is_null($file)) {
            return new JsonResponse([
                'message' => 'File not exist'
            ], 200);
        }
        return new JsonResponse([
            'id' => $file->getId(),
            'path' => $file->getPath(),
            'size' => $file->getSize(),
            'title' => $file->getTitle(),
            'type' => $file->getType(),
            'created_at' => $file->getCreatedAt()
        ], 200);
    }

    /**
     * @Route("/files/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteFile($id)
    {
        $file = $this->getDoctrine()->getRepository(Files::class)->find($id);
        if (is_null($file)) {
            return new JsonResponse([
                'message' => 'File not exist'
            ], 200);
        }
        $this->removeFile($file->getPath());
        $em = $this->getDoctrine()->getManager();
        $requests = $file->getRequest();
        $em->remove($file);
        if (!is_null($requests)) {
            $em->remove($requests);
        }
        $em->flush();
        return new JsonResponse([
            'message' => 'Файл удален'
        ], 200);
    }

    /**
     * @Route("/files", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getFiles(Request $request)
    {

        $response = [];
        $search['filter'] = $request->get('filter')?$request->get('filter'):null;
        $search['search'] = $request->get('search')?$request->get('search'):null;
        /**
         * @var $user Users
         */
        $user = $this->getUser();
        $files = $this->getDoctrine()->getRepository(Files::class)->filterBy(
            $user,
            $this->sorting($request),
            $this->getLimit($request),
            $this->getOffset($request),
            $search
        );
        if (is_null($files)) {
            return new JsonResponse([
                'message' => 'File not exist'
            ], 200);
        }
        foreach ($files as $file) {
            $data = [
                'id' => $file->getId(),
                'path' => $file->getPath(),
                'size' => $file->getSize(),
                'title' => $file->getTitle(),
                'type' => $file->getType(),
                'updated_at' => $file->getUpdatedAt(),
                'request' => null
            ];
            if (!is_null($userRequest = $file->getRequest())) {
                $data['request'] = [
                    'id' => $userRequest->getId(),
                    'status' => $userRequest->getStatus(),
                    'requirement' => $userRequest->getRequirement()->getTitle(),
                    'created' => $userRequest->getCreatedAt()
                ];
            }
            $response[] = $data;
        }
        /**
         * @var $user Users
         */
        $user = $this->getUser();
        return new JsonResponse([
            'response' => $response,
            'total' => $this->getDoctrine()->getRepository(Files::class)->total($user, $search)
        ], 200);
    }

    /**
     * @param $file
     */
    public function removeFile($file)
    {
        $filesystem = new Filesystem();
        $filesystem->remove([$this->getParameter('kernel.project_dir') . '/public' . $file]);
    }
}

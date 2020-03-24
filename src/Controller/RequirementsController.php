<?php

namespace App\Controller;

use App\Entity\Requirements;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class RequirementsController
 * @package App\Controller
 * @Route("/api")
 */
class RequirementsController extends ApiController
{
    /**
     * @Route("/requirements", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $items = $this->getDoctrine()->getRepository(Requirements::class)->findBy([
                $this->sorting($request),
                $this->getLimit($request),
                $this->getOffset($request)
            ]);
        } else {
            $items = $this->getDoctrine()->getRepository(Requirements::class)->findBy([
                "user_created" => $this->getUser()->getId(),
                $this->sorting($request),
                $this->getLimit($request),
                $this->getOffset($request)
            ]);
        }

        $data = [];
        if (is_null($items)) {
            return new JsonResponse([
                'message' => 'Нет текущих запросов'
            ]);
        }

        foreach ($items as $item) {
            $temp = [
                'id' => $item->getId(),
                'user_created' => null,
                'created_at' => $item->getCreatedAt(),
                'updated_at' => $item->getUpdatedAt(),
                'fields' => $item->getFields()
            ];
            if (!is_null($user = $item->getUserCreated())) {
                $temp['user'] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername()
                ];
            }
            $data[] = $temp;
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/requirements", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        } else {
            return new JsonResponse([
                'response' => 'no data for create'
            ]);
        }
        if (array_key_exists('fields', $parametersAsArray)) {
            $fields = $parametersAsArray['fields'];
        } else {
            $fields = null;
        }

        $req = new Requirements();
        $req->setFields($fields);
        $req->setUserCreated($this->getUser());

        $em->persist($req);
        $em->flush();
        return new JsonResponse([
            'response' => 'Created requirements'
        ]);
    }

    /**
     * @Route("/requirements/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $item = $this->getDoctrine()->getRepository(Requirements::class)->find($id);
        if (is_null($item)) {
            return new JsonResponse([
                'response' => 'No content'
            ], 203);
        }
        $data = [
            'id' => $item->getId(),
            'user_created' => null,
            'created_at' => $item->getCreatedAt(),
            'updated_at' => $item->getUpdatedAt(),
            'fields' => $item->getFields()
        ];
        if (!is_null($user = $item->getUserCreated())) {
            $data['user'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/requirements/{id}", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function edit($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getDoctrine()->getRepository(Requirements::class)->find($id);
        if (is_null($req)) {
            return new JsonResponse([
                'response' => 'No content'
            ], 203);
        }
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        } else {
            return new JsonResponse([
                'response' => 'no data for create'
            ]);
        }
        if (array_key_exists('fields', $parametersAsArray)) {
            $fields = $parametersAsArray['fields'];
        } else {
            $fields = null;
        }

        $req->setFields($fields);
        $req->setUserCreated($this->getUser());

        $em->persist($req);
        $em->flush();
        return new JsonResponse([
            'response' => 'Updated item'
        ], 203);
    }

    /**
     * @Route("/requirements/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $this->getDoctrine()->getRepository(Requirements::class)->find($id);
        if (is_null($item)) {
            return new JsonResponse([
                'response' => 'No content'
            ], 203);
        }
        $em->remove($item);
        $em->flush();
        return new JsonResponse([
            'response' => 'Deleted item'
        ], 200);
    }
}

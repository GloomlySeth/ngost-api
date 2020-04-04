<?php

namespace App\Controller;

use App\Entity\Files;
use App\Entity\Requirements;
use App\Entity\UserRequest;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserRequestController
 * @package App\Controller
 * @Route("/api")
 */
class UserRequestController extends ApiController
{
    /**
     * @Route("/user/requests", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());

        $requests = $this->getDoctrine()->getRepository(UserRequest::class)->findBy(
            ['user' => $user],
            $this->sorting($request),
            $this->getLimit($request),
            $this->getOffset($request)
        );
        if (is_null($requests)) {
            return new JsonResponse([
                'message' => 'Requests not found'
            ]);
        }

        $data = [];

        foreach ($requests as $request) {
            $item = [
                'id' => $request->getId(),
                'created_at' => $request->getCreatedAt(),
                'requirement' => null,
                'status' => $request->getStatus()
            ];

            if (!is_null($requirement = $request->getRequirement())) {
                $item['requirement'] = [
                    'id' => $requirement->getId(),
                    'fields' => $requirement->getFields(),
                    'created_at' => $requirement->getCreatedAt(),
                    'updated_at' => $requirement->getUpdatedAt()
                ];
            }


            $data[] = $item;
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/user/requests/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());

        $requests = $this->getDoctrine()->getRepository(UserRequest::class)->findOneBy([
            'id' => $id,
            'user' => $user
        ]);
        if (is_null($requests)) {
            return new JsonResponse([
                'message' => 'Requests not found'
            ]);
        }

        $item = [
            'id' => $requests->getId(),
            'created_at' => $requests->getCreatedAt(),
            'requirement' => null,
            'status' => $requests->getStatus()
        ];

        if (!is_null($requirement = $requests->getRequirement())) {
            $item['requirement'] = [
                'id' => $requirement->getId(),
                'fields' => $requirement->getFields(),
                'created_at' => $requirement->getCreatedAt(),
                'updated_at' => $requirement->getUpdatedAt()
            ];
        }

        return new JsonResponse([
            'response' => $item
        ]);
    }

    /**
     * @Route("/user/requests", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorInterface $validator)
    {
        /**
         * @var $user Users
         */
        $user = $this->getUser();
        $parametersAsArray = [];
        $em = $this->getDoctrine()->getManager();
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        if (array_key_exists('file', $parametersAsArray)) {
            $file = $parametersAsArray['file'];
        } else {
            $file = null;
        }
        if (array_key_exists('requirement', $parametersAsArray)) {
            $requirement = $parametersAsArray['requirement'];
        } else {
            $requirement = null;
        }
        $requirement = $this->getDoctrine()->getRepository(Requirements::class)->find($requirement);
        $file = $this->getDoctrine()->getRepository(Files::class)->find($file);
        $userRequest = new UserRequest();
        $userRequest->setRequirement($requirement);
        $userRequest->setUser($user);
        $userRequest->setStatus("0");
        $errors = $validator->validate($userRequest);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }

        $em->persist($userRequest);
        $file->setRequest($userRequest);
        $em->persist($file);
        $em->flush();

        return new JsonResponse([
            'message' => 'Создан новый запрос на обработку документа'
        ]);
    }


    /**
     * @Route("/user/requests/{id}", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorInterface $validator, $id)
    {
        /**
         * @var $user Users
         */
        $user = $this->getUser();
        $parametersAsArray = [];
        $em = $this->getDoctrine()->getManager();
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        if (array_key_exists('file', $parametersAsArray)) {
            $file = $parametersAsArray['file'];
        } else {
            $file = null;
        }
        if (array_key_exists('requirement', $parametersAsArray)) {
            $requirement = $parametersAsArray['requirement'];
        } else {
            $requirement = null;
        }


        $userRequest = $this->getDoctrine()->getRepository(UserRequest::class)->findOneBy([
            'id' => $id,
            'user' => $user
        ]);
        if (is_null($userRequest)) {
            return new JsonResponse([
                'message' => 'Requests not found'
            ]);
        }
        $requirement = $this->getDoctrine()->getRepository(Requirements::class)->find($requirement);
        $file = $this->getDoctrine()->getRepository(Files::class)->find($file);
        $userRequest->setFile($file);
        $userRequest->setRequirement($requirement);
        $userRequest->setUser($user);
        $errors = $validator->validate($userRequest);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }

        $em->persist($userRequest);
        $file->setRequest($userRequest);
        $em->persist($file);
        $em->flush();

        return new JsonResponse([
            'message' => 'Update user request'
        ]);
    }


    /**
     * @Route("/user/requests/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $user = $this->getDoctrine()->getRepository(Users::class)->find($this->getUser()->getId());

        $userRequest = $this->getDoctrine()->getRepository(UserRequest::class)->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if (is_null($userRequest)) {
            return new JsonResponse([
                'message' => 'Requests not found'
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        foreach ($userRequest->getFiles() as $req) {
            $userRequest->removeFile($req);
        }
        $em->remove($userRequest);
        $em->flush();

        return new JsonResponse([
            'message' => 'Удален запрос на обработку документа'
        ]);
    }

    /**
     * @Route("/user/requests/{id}/status/{status}", methods={"GET"})
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function setStatus($id, $status)
    {

        $userRequest = $this->getDoctrine()->getRepository(UserRequest::class)->find($id);

        if (is_null($userRequest)) {
            return new JsonResponse([
                'message' => 'Requests not found'
            ]);
        }
        $userRequest->setStatus($status);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userRequest);
        $em->flush();

        return new JsonResponse([
            'message' => 'Status update'
        ]);
    }

    /**
     * @Route("/user/requests/{id}/rollback", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function rollbackFile($id)
    {
        $file = $this->getDoctrine()->getRepository(Files::class)->find($id);
        if (is_null($file)) {
            return new JsonResponse([
                'message' => 'Requests not exist'
            ], 200);
        }
        $request = $file->getRequest();
        $em = $this->getDoctrine()->getManager();
        $file->setRequest(null);
        $em->remove($request);
        $em->flush();
        return new JsonResponse([
            'message' => 'Запрос отменен'
        ], 200);
    }
}

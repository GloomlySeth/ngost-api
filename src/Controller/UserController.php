<?php

namespace App\Controller;

use App\Entity\Users;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/api")
 * @IsGranted({"ROLE_USER"})
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", methods={"GET"})
     */
    public function index()
    {
        $user = $this->getUser();
        $response = [
            "role" => $user->getRoles(),
            "username" => $user->getUsername()
        ];
        return new JsonResponse([
            "user" => $response
        ], 200);
    }

    /**
     * @Route("/users", methods={"GET"})
     */
    public function all()
    {
        $data = $this->getDoctrine()->getRepository(Users::class)->findAll();
        $result = [];
        foreach ($data as $item) {
            $response = [
                "id" => $item->getId(),
                "username" => $item->getUsername(),
                "branch" => null,
                "roles" => $item->getRoles()
            ];
            $result[] = $response;
        }
        return new JsonResponse([
            "response" => $result
        ]);
    }
    /**
     * @Route("/users/search/{search}", methods={"GET"})
     * @param $search
     * @return JsonResponse
     */
    public function search($search)
    {
        $data = $this->getDoctrine()->getRepository(Users::class)->findBy(["username" => $search]);
        $result = [];
        foreach ($data as $item) {
            $response = [
                "id" => $item->getId(),
                "username" => $item->getUsername(),
                "roles" => $item->getRoles()
            ];
            $result[] = $response;
        }
        return new JsonResponse([
            "response" => $result
        ], 200);
    }

    /**
     * @Route("/users/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $item = $this->getDoctrine()->getRepository(Users::class)->find($id);
        $response = [
            "id" => $item->getId(),
            "username" => $item->getUsername(),
            "roles" => $item->getRoles()
        ];
        return new JsonResponse([
            "response" => $response
        ], 200);
    }

    /**
     * @Route("/users/{id}", methods={"PUT"})
     * @param Request $request
     * @param $id
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function update(Request $request, $id,UserPasswordEncoderInterface $encoder)
    {

        $item = $this->getDoctrine()->getRepository(Users::class)->find($id);
        if (is_null($item)) {
            return new JsonResponse([
                "message" => "No item id" . $id
            ], 200);
        }
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $username = array_key_exists('username', $parametersAsArray)?$parametersAsArray['username']:null;
        $password = $parametersAsArray['password'];
        $role = $parametersAsArray['roles'];

        if (!is_null($username)) {
            $item->setUsername($username);
        }
        if (!is_null($password)) {
            $item->setPassword($encoder->encodePassword($item, $password));
        }
        if (!is_null($role)) {
            if (is_array($role)) {
                $item->setRoles($role);
            }
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();
        return new JsonResponse(["message" => "Updated user ". $item->getUsername()], 201);
    }

    /**
     * @Route("/users", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function create(Request $request, UserPasswordEncoderInterface $encoder)
    {

        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $username = $parametersAsArray['username'];
        $password = $parametersAsArray['password'];
        $role = $parametersAsArray['roles'];

        $item = new Users();
        if (!is_null($username)) {
            $item->setUsername($username);
        }
        if (!is_null($password)) {
            $item->setPassword($encoder->encodePassword($item, $password));
        }
        if (!is_null($role)) {
            if (is_array($role)) {
                $item->setRoles($role);
            } else {
                $item->setRoles([$role]);
            }
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return new JsonResponse(["message" => "Добавлен новаый пользователь ". $item->getUsername()], 201);
    }
}
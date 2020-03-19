<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Place;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/api")
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
            "username" => $user->getUsername(),
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
        $em = $this->getDoctrine()->getManager();
        $username = $parametersAsArray['username'];
        $password = $parametersAsArray['password'];
        $role = $parametersAsArray['roles'];
        $type = $parametersAsArray['type'] == 2 ? true : false;
        $org = null;
        if ($type) {
            $org = $parametersAsArray['org'];
        }
        $item = new Users();
        $item->setCompany($type);
        if (!is_null($username)) {
            $item->setUsername($username);
        }
        if (!is_null($org)) {
           foreach ($org['place'] as $place) {
               $places = new Place();
               $places->setAddress($place['address']);
               $places->setCity($place['city']);
               $places->setCountry($place['country']);
               $places->setType($place['type']);
               $places->setPostcode($place['postcode']);
               $places->setUser($item);
               $em->persist($places);
           }
           $contact = new Contact();
           $contact->setFullTitle($org['full_title']);
           $contact->setAbbreviation($org['abbreviation']);

           $contact->setFullName($org['leadership']['full_name']);
           $contact->setBase($org['leadership']['base']);
           $contact->setPosition($org['leadership']['position']);

            $contact->setEmail($org['contact']['email']);
            $contact->setPhone($org['contact']['phone']);
            $contact->setFax($org['contact']['fax']);

            $contact->setAddress($org['bank']['address']);
            $contact->setKod($org['bank']['kod']);
            $contact->setBank($org['bank']['bank']);
            $contact->setPayment($org['bank']['payment']);
            $contact->setOkpo($org['bank']['okpo']);
            $contact->setUnn($org['bank']['unn']);
            $contact->setUser($item);
            $em->persist($contact);
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

        $em->persist($item);
        $em->flush();

        return new JsonResponse($parametersAsArray);
    }
}
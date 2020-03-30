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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        /**
         *  @var Users $user
         */
        $user = $this->getUser();
        $response = [
            "id" => $user->getId(),
            "password" => null,
            "role" => $user->getRoles(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "phone" => $user->getPhone(),
            "mailing" => $user->getMailing(),
            "alerts" => $user->getAlerts(),
            "org" => null,
            "type" => $user->getCompany(),
        ];
        if ($user->getContacts()) {
            $contacts = $user->getContacts();
            foreach ($contacts as $contact) {
                $response['org'] = [
                    'contact_id' => $contact->getId(),
                    'abbreviation' => $contact->getAbbreviation(),
                    'full_title' => $contact->getFullTitle(),
                    'place' => [],
                    'leadership' => [
                        'full_name' => $contact->getFullName(),
                        'base' => $contact->getBase(),
                        'position' => $contact->getPosition(),
                    ],
                    'contact' => [
                        'email' => $contact->getEmail(),
                        'phone' => $contact->getPhone(),
                        'fax' => $contact->getFax(),
                    ],
                    'bank' => [
                        'address' => $contact->getAddress(),
                        'kod' => $contact->getKod(),
                        'bank' => $contact->getBank(),
                        'payment' => $contact->getPayment(),
                        'okpo' => $contact->getOkpo(),
                        'unn' => $contact->getUnn(),
                    ],
                ] ;
            }

        } else {
            $response['org'] = [
                'contact_id' => null,
                'abbreviation' => null,
                'full_title' => null,
                'leadership' => [
                    'full_name' => null,
                    'base' => null,
                    'position' => null,
                ],
                'contact' => [
                    'email' => null,
                    'phone' => null,
                    'fax' => null,
                ],
                'bank' => [
                    'address' => null,
                    'kod' => null,
                    'bank' => null,
                    'payment' => null,
                    'okpo' => null,
                    'unn' => null,
                ],
            ] ;
        }
        if ($places = $user->getPlaces()) {
            foreach ($places as $place) {
                array_push($response['org']['place'], [
                    'id' => $place->getId(),
                    'type' => $place->getType(),
                    'address' => $place->getAddress(),
                    'postcode' => $place->getPostcode(),
                    'city' => $place->getCity(),
                    'country' => $place->getCountry(),
                ]);
            }
        } else {
            array_push($response['org']['place'], [
                'type' => 1,
                'id' => null,
                'address' => null,
                'postcode' => null,
                'city' => null,
                'country' => null,
            ]);
            array_push($response['org']['place'], [
                'type' => 2,
                'id' => null,
                'address' => null,
                'postcode' => null,
                'city' => null,
                'country' => null,
            ]);
        }
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
        foreach ($data as $user) {
            $response = [
                "id" => $user->getId(),
                "password" => null,
                "role" => $user->getRoles(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "phone" => $user->getPhone(),
                "mailing" => $user->getMailing(),
                "alerts" => $user->getAlerts(),
                "org" => null,
                "type" => $user->getCompany(),
            ];
            if ($user->getContacts()) {
                $contacts = $user->getContacts();
                foreach ($contacts as $contact) {
                    $response['org'] = [
                        'contact_id' => $contact->getId(),
                        'abbreviation' => $contact->getAbbreviation(),
                        'full_title' => $contact->getFullTitle(),
                        'place' => [],
                        'leadership' => [
                            'full_name' => $contact->getFullName(),
                            'base' => $contact->getBase(),
                            'position' => $contact->getPosition(),
                        ],
                        'contact' => [
                            'email' => $contact->getEmail(),
                            'phone' => $contact->getPhone(),
                            'fax' => $contact->getFax(),
                        ],
                        'bank' => [
                            'address' => $contact->getAddress(),
                            'kod' => $contact->getKod(),
                            'bank' => $contact->getBank(),
                            'payment' => $contact->getPayment(),
                            'okpo' => $contact->getOkpo(),
                            'unn' => $contact->getUnn(),
                        ],
                    ] ;
                }

            } else {
                $response['org'] = [
                    'contact_id' => null,
                    'abbreviation' => null,
                    'full_title' => null,
                    'leadership' => [
                        'full_name' => null,
                        'base' => null,
                        'position' => null,
                    ],
                    'contact' => [
                        'email' => null,
                        'phone' => null,
                        'fax' => null,
                    ],
                    'bank' => [
                        'address' => null,
                        'kod' => null,
                        'bank' => null,
                        'payment' => null,
                        'okpo' => null,
                        'unn' => null,
                    ],
                ] ;
            }
            if ($places = $user->getPlaces()) {
                foreach ($places as $place) {
                    array_push($response['org']['place'], [
                        'id' => $place->getId(),
                        'type' => $place->getType(),
                        'address' => $place->getAddress(),
                        'postcode' => $place->getPostcode(),
                        'city' => $place->getCity(),
                        'country' => $place->getCountry(),
                    ]);
                }
            } else {
                array_push($response['org']['place'], [
                    'type' => 1,
                    'id' => null,
                    'address' => null,
                    'postcode' => null,
                    'city' => null,
                    'country' => null,
                ]);
                array_push($response['org']['place'], [
                    'type' => 2,
                    'id' => null,
                    'address' => null,
                    'postcode' => null,
                    'city' => null,
                    'country' => null,
                ]);
            }
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
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function update(Request $request, $id,UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
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
        $email = array_key_exists('email', $parametersAsArray)?$parametersAsArray['email']:null;
        $role = array_key_exists('role',$parametersAsArray)?$parametersAsArray['role']:null;
        $phone = array_key_exists('phone',$parametersAsArray)?$parametersAsArray['phone']:null;
        $password = array_key_exists('password',$parametersAsArray)?$parametersAsArray['password']:null;
        $type = $parametersAsArray['type'] == 2 ? true : false;
        $org = null;
        if ($type) {
            $org = $parametersAsArray['org'];
            $item->setRoles(['ROLE_USER', 'ROLE_COMPANY']);
        }
        if (!empty($password)){
            $item->setPassword($encoder->encodePassword($item, $password));
        }
        $item->setCompany($type);
        $item->setMailing($parametersAsArray['mailing']??false);
        $item->setAlerts($parametersAsArray['alerts']??false);
        if (!is_null($username)) {
            $item->setUsername($username);
        }
        $item->setEmail($email);
        $item->setPhone($phone);
        if (!is_null($role)) {
            if (is_array($role)) {
                $item->setRoles($role);
            }
        }
        $errors = $validator->validate($item);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        if (!is_null($org)) {
            foreach ($org['place'] as $place) {
                $places = $this->getDoctrine()->getRepository(Place::class)->find($place['id']);
                if (is_null($places)) {
                    $places = new Place();
                }
                $places->setAddress($place['address']);
                $places->setCity($place['city']);
                $places->setCountry($place['country']);
                $places->setType($place['type']);
                $places->setPostcode($place['postcode']);
                $places->setUser($item);
                $em->persist($places);
            }
            $contact = $this->getDoctrine()->getRepository(Contact::class)->find($org['contact_id']);
            if (is_null($contact)) {
                $contact = new Contact();
            }
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

    /**
     * @Route("/users/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete ($id) {
        $user = $this->getDoctrine()->getRepository(Users::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return new JsonResponse(["message" => "Delete user ". $user->getUsername()], 200);
    }
}
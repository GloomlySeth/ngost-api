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

class AuthController extends AbstractController
{

    private $username;
    private $password;
    private $email;
    private $phone;


    /**
     * @Route("/api/register", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $this->setUsername($parametersAsArray);
        $this->setPassword($parametersAsArray);
        $this->setEmail($parametersAsArray);
        $this->setPhone($parametersAsArray);

        $em = $this->getDoctrine()->getManager();

        $user = new Users();
        if (!is_null($this->username)) {
            $user->setUsername($this->username);
        }
        if (!is_null($this->email)){
            $user->setEmail($this->email);
        }
        if (!is_null($this->phone)){
            $user->setPhone($this->phone);
        }
        if (!is_null($this->password)){
            $user->setPassword($encoder->encodePassword($user, $this->password));
        }
        $type = $parametersAsArray['type'] == 2 ? true : false;
        $org = null;
        if ($type) {
            $org = $parametersAsArray['org'];
            $user->setRoles(['ROLE_USER', 'ROLE_COMPANY']);
        }
        $user->setCompany($type);
        $user->setMailing($parametersAsArray['mailing']);
        $user->setAlerts($parametersAsArray['alerts']);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }
        $em->persist($user);
        $em->flush();
        if (!is_null($org)) {
            foreach ($org['place'] as $place) {
                $places = new Place();
                $places->setAddress($place['address']);
                $places->setCity($place['city']);
                $places->setCountry($place['country']);
                $places->setType($place['type']);
                $places->setPostcode($place['postcode']);
                $places->setUser($user);
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
            $contact->setUser($user);
            $em->persist($contact);
        }
        $em->flush();

        return new JsonResponse([
            'message' => 'Регистрация прошла успешно',
            'id' => $user->getId()
        ], 201);
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        if (array_key_exists('username', $username)) {
            $this->username = $username['username'];
        } else {
            $this->username = null;
        }
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        if (array_key_exists('password', $password)) {
            $this->password = $password['password'];
        } else {
            $this->password = null;
        }
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        if (array_key_exists('email', $email)) {
            $this->email = $email['email'];
        } else {
            $this->email = null;
        }
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        if (array_key_exists('phone', $phone)) {
            $this->phone = $phone['phone'];
        } else {
            $this->phone = null;
        }
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
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

    public function __construct(Request $request)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $this->setUsername($parametersAsArray);
        $this->setPassword($parametersAsArray);
        $this->setEmail($parametersAsArray);
        $this->setPhone($parametersAsArray);
    }

    /**
     * @Route("/register", methods={"POST"})
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function index(UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {

        $em = $this->getDoctrine()->getManager();

        $user = new Users();
        $user->setUsername($this->username);
        $user->setEmail($this->email);
        $user->setEmail($this->phone);
        $user->setPassword($encoder->encodePassword($user, $this->password));

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

        return new JsonResponse([
            'message' => 'Добавлен новый пользователь',
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

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
    /**
     * @Route("/register", methods={"POST"})
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
        $em = $this->getDoctrine()->getManager();
        $username = $parametersAsArray['username'];
        $password = $parametersAsArray['password'];
        $email = $parametersAsArray['email'];
        $phone = $parametersAsArray['phone'];
        $user = new Users();
        if (!is_null($username)) {
            $user->setUsername($username);
        }
        if (!is_null($email)) {
            $user->setEmail($email);
        }
        if (!is_null($phone)) {
            $user->setEmail($phone);
        }
        if (!is_null($password)) {
            $user->setPassword($encoder->encodePassword($user, $password));
        }
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
}

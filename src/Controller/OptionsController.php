<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Options;
use App\Entity\Place;
use App\Entity\Users;
use Ausi\SlugGenerator\SlugGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class OptionsController
 * @package App\Controller
 * @Route("/api")
 */
class OptionsController extends ApiController
{
    /**
     * @Route("/options", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getOptions(Request $request) {
        $options = $this->getDoctrine()->getRepository(Options::class)->findAll();
        $response = [];
        foreach ($options as $option) {
            $response[] = [
              "id" => $option->getId(),
              "title" => $option->getTitle(),
              "slug" => $option->getSlug(),
              "options" => $option->getOptions()
            ];
        }
        return new JsonResponse([
            "response" => $response
        ]);
    }

    /**
     * @Route("/options/{slug}", methods={"GET"})
     * @param $slug
     * @return JsonResponse
     */
    public function getOption ($slug) {
        $option = $this->getDoctrine()->getRepository(Options::class)->findOneBy(["slug" => $slug]);
        $response = [
            "id" => $option->getId(),
            "title" => $option->getTitle(),
            "slug" => $option->getSlug(),
            "options" => $option->getOptions()
        ];
        return new JsonResponse([
            "response" => $response
        ]);
    }

    /**
     * @Route("/options", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createOption (Request $request, ValidatorInterface $validator) {
        $em = $this->getDoctrine()->getManager();
        $params = $this->getRequest($request);
        $option = new Options();
        $option->setOptions($params["options"]);
        $option->setTitle($params["title"]);
        $generator = new SlugGenerator();
        $option->setSlug($generator->generate($params["title"]));
        $errors = $validator->validate($option);
        if (count($errors) > 0) {
            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }
        $em->persist($option);
        $em->flush();
        return new JsonResponse([
            'message' => 'Создан новый набор опций'
        ], 201);
    }

    /**
     * @Route("/options/{slug}/edit", methods={"POST"})
     * @param $slug
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function updateOption ($slug, Request $request, ValidatorInterface $validator) {
        $em = $this->getDoctrine()->getManager();
        $params = $this->getRequest($request);
        $option = $this->getDoctrine()->getRepository(Options::class)->findOneBy(["slug" => $slug]);

        $option->setOptions($params["options"]);
        $option->setTitle($params["title"]);
        $option->setSlug($params["slug"]);

        $errors = $validator->validate($option);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }
        $em->persist($option);
        $em->flush();
        return new JsonResponse([
            'message' => 'Изменен набор опций'
        ], 201);
    }

    /**
     * @Route("/options/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteOption ($id) {
        if (!$this->isGranted("ROLE_ADMIN")) {
            return new JsonResponse([
                'message' => 'Недостаточно прав'
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $option = $this->getDoctrine()->getRepository(Options::class)->find($id);

        $em->remove($option);
        $em->flush();
        return new JsonResponse([
            'message' => 'Удален набор опций'
        ]);
    }

    public function getRequest (Request $request) {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        if (array_key_exists('slug', $parametersAsArray)) {
            $slug = $parametersAsArray['slug'];
        } else {
            $slug = null;
        }
        if (array_key_exists('title', $parametersAsArray)) {
            $title = $parametersAsArray['title'];
        } else {
            $title = null;
        }
        if (array_key_exists('options', $parametersAsArray)) {
            $options = $parametersAsArray['options'];
        } else {
            $options = null;
        }
        return [
            "title" => $title,
            "slug" => $slug,
            "options" => $options
        ];
    }
}

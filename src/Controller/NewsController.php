<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Pages;
use Ausi\SlugGenerator\SlugGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PagesController
 * @package App\Controller
 * @Route("/api")
 */
class NewsController extends ApiController
{
    /**
     * @Route("/news", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $news = $this->getDoctrine()->getRepository(News::class)->findBy(
                ['deleted_at' => null],
                $this->sorting($request),
                $this->getLimit($request),
                $this->getOffset($request)
            );
        $data = [];
        foreach ($news as $new) {
            $data[] = [
                'id' => $new->getId(),
                'title' => $new->getTitle(),
                'description' => $new->getDescription(),
                'image' => $new->getImage(),
                'created_at' => $new->getCreatedAt(),
                'short_desc' => $new->getShortDesc(),
                'created_user' => null
            ];
            if(!is_null($user = $new->getCreatedUser())) {
                $data['created_user'] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername()
                ];
            }
        }
        return new JsonResponse([
            'data' => $data
        ]);
    }

    /**
     * @Route("/news", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */

    public function createNew(Request $request,  ValidatorInterface $validator)
    {
        $parametersAsArray = [];
        $em = $this->getDoctrine()->getManager();
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        if (array_key_exists('title', $parametersAsArray)) {
            $title = $parametersAsArray['title'];
        } else {
            $title = null;
        }
        if (array_key_exists('content', $parametersAsArray)) {
            $content = $parametersAsArray['content'];
        } else {
            $content = null;
        }
        if (array_key_exists('name', $parametersAsArray)) {
            $name = $parametersAsArray['name'];
        } else {
            $name = null;
        }
        if (array_key_exists('header', $parametersAsArray)) {
            $header = $parametersAsArray['header'];
        } else {
            $header = null;
        }
        if (array_key_exists('status', $parametersAsArray)) {
            $status = $parametersAsArray['status'];
        } else {
            $status = 'draft';
        }
        $generator = new SlugGenerator();
        $page = new Pages();
        $page->setTitle($title);
        $page->setName($name);
        $page->setSlug($generator->generate($name));
        $page->setHeader($header);
        $page->setContent($content);

        $page->setUserUpdated($this->getUser());
        $page->setStatus($status);
        $errors = $validator->validate($page);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }

        $em->persist($page);
        $em->flush();
        return new JsonResponse([
            'message' => 'add new page',
            'id' => $page->getId()
        ]);
    }
}

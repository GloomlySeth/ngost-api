<?php

namespace App\Controller;

use App\Entity\Pages;
use Ausi\SlugGenerator\SlugGenerator;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PagesController
 * @package App\Controller
 * @Route("/api")
 */
class PagesController extends ApiController
{
    /**
     * @Route("/pages", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $pages = $this->getDoctrine()->getRepository(Pages::class)
            ->findBy(
                ['deleted_at' => null],
                $this->sorting($request),
                $this->getLimit($request),
                $this->getOffset($request)
            );
        $data = [];
        foreach ($pages as $page) {
            $response = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'name' => $page->getName(),
                'header' => $page->getHeader(),
                'slug' => $page->getSlug(),
                'content' => $page->getContent(),
                'user_updated' => null
            ];
            if(!is_null($user = $page->getUserUpdated())) {
                $response['user_updated'] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername()
                ];
            }
            $data[] = $response;
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/pages/{slug}", methods={"GET"})
     * @param $slug
     * @return JsonResponse
     */
    public function show($slug)
    {
        $page = $this->getDoctrine()->getRepository(Pages::class)
            ->findOneBy(['slug' => $slug]);
        $data = [];
        $data[] = [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'name' => $page->getName(),
            'header' => $page->getHeader(),
            'slug' => $page->getSlug(),
            'content' => $page->getContent(),
            'user_updated' => null
        ];
        if(!is_null($user = $page->getUserUpdated())) {
            $data['user_updated'] = [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/pages", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */

    public function createPage(Request $request,  ValidatorInterface $validator)
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

    /**
     * @Route("/pages/{id}/edit", methods={"POST"})
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */

    public function updatePage($id,Request $request,  ValidatorInterface $validator)
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
        $page = $this->getDoctrine()->getRepository(Pages::class)->find($id);
        if (empty($page)) {
            return new JsonResponse([
                'message' => 'Pages not found'
            ], 203);
        }
        $page->setTitle($title);
        $page->setName($name);
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
            'message' => 'edit page',
            'id' => $page->getSlug()
        ]);
    }

    /**
     * @Route("/pages/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function deletePage($id)
    {

        $em = $this->getDoctrine()->getManager();
        $new = $this->getDoctrine()->getRepository(Pages::class)->find($id);
        if (!empty($new)){
            $new->setDeletedAt(new DateTime());
            $em->persist($new);
            $em->flush();
            return new JsonResponse([
                'message' => 'Delete news'
            ]);
        }
        return new JsonResponse([
            'message' => 'News not found'
        ]);
    }
}

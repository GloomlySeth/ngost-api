<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\News;
use Ausi\SlugGenerator\SlugGenerator;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class NewsController
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
                'image' => null,
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
            if(!is_null($image = $new->getImage())) {
                $data['image'] = [
                    'id' => $image->getId(),
                    'file_path' => $image->getFilePath(),
                    'file_name' => $image->getFileName(),
                    'file_type' => $image->getFileType()
                ];
            }
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/news/{id}", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function showNews($id)
    {
        $new = $this->getDoctrine()->getRepository(News::class)->find($id);
        $data = [];
        $data[] = [
            'id' => $new->getId(),
            'title' => $new->getTitle(),
            'description' => $new->getDescription(),
            'image' => null,
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
        if(!is_null($image = $new->getImage())) {
            $data['image'] = [
                'id' => $image->getId(),
                'file_path' => $image->getFilePath(),
                'file_name' => $image->getFileName(),
                'file_type' => $image->getFileType()
            ];
        }
        return new JsonResponse([
            'response' => $data
        ]);
    }

    /**
     * @Route("/news/{id}", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteNew($id)
    {

        $em = $this->getDoctrine()->getManager();
        $new = $this->getDoctrine()->getRepository(News::class)->find($id);
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

        if (array_key_exists('description', $parametersAsArray)) {
            $description = $parametersAsArray['description'];
        } else {
            $description = null;
        }

        if (array_key_exists('shortDesc', $parametersAsArray)) {
            $shortDesc = $parametersAsArray['shortDesc'];
        } else {
            $shortDesc = null;
        }

        if (array_key_exists('image', $parametersAsArray)) {
            $image = $parametersAsArray['image'];
            if (!empty($image)) {
                $image = $this->getDoctrine()->getRepository(Media::class)->find($image);
            } else {
                $image = null;
            }
        } else {
            $image = null;
        }


        $generator = new SlugGenerator();
        $new = new News();
        $new->setTitle($title);
        $new->setDescription($description);
        $new->setSlug($generator->generate($title));
        $new->setShortDesc($shortDesc);
        $new->setImage($image);

        $new->setCreatedUser($this->getUser());
        $errors = $validator->validate($new);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }

        $em->persist($new);
        $em->flush();
        return new JsonResponse([
            'message' => 'add new',
            'id' => $new->getSlug()
        ]);
    }

    /**
     * @Route("/news/{id}/edit", methods={"POST"})
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */

    public function updateNew($id,Request $request,  ValidatorInterface $validator)
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
        if (array_key_exists('image', $parametersAsArray)) {
            $image = $parametersAsArray['image'];
            if (!empty($image)) {
                $image = $this->getDoctrine()->getRepository(Media::class)->find($image);
            } else {
                $image = null;
            }
        } else {
            $image = null;
        }
        if (array_key_exists('description', $parametersAsArray)) {
            $description = $parametersAsArray['description'];
        } else {
            $description = null;
        }

        if (array_key_exists('shortDesc', $parametersAsArray)) {
            $shortDesc = $parametersAsArray['shortDesc'];
        } else {
            $shortDesc = null;
        }

        $new = $this->getDoctrine()->getRepository(News::class)->find($id);
        if (empty($new)) {
            return new JsonResponse([
                'message' => 'News not found'
            ], 203);
        }
        $new->setTitle($title);
        $new->setDescription($description);
        $new->setShortDesc($shortDesc);
        $new->setImage($image);

        $errors = $validator->validate($new);
        if (count($errors) > 0) {

            $errorsString = [];
            foreach ($errors as $error) {
                $errorsString[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse($errorsString, 203);
        }

        $em->persist($new);
        $em->flush();
        return new JsonResponse([
            'message' => 'add new',
            'id' => $new->getSlug()
        ]);
    }
}

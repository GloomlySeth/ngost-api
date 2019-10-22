<?php

namespace App\Controller;

use App\Entity\Pages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
                [],
                $this->sorting($request),
                $this->getLimit($request),
                $this->getOffset($request)
            );
        $data = [];
        foreach ($pages as $page) {
            $data[] = [
                'id' => $page->getId(),
                'title' => $page->getTitle(),
                'content' => $page->getContent(),
                'user_updated' => null
            ];
        }
        return new JsonResponse([
            'data' => $data
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('api/pages', name: 'page_index')]
    public function index()
    {
        $pages = $this->repository->findAll();
        return $this->json($pages);
    }

    #[Route('api/pages/{id}', name: 'pages_view')]
    public function view($id): Response
    {
        $page = $this->repository->find($id);
        return $this->json($page);
    }
}
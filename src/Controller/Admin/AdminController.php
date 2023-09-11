<?php
namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;

class AdminController{

    /**
     * @var Environment
     */
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function home(): Response
    {
        return new Response($this->twig->render('admin/home.html.twig'));
    }
}
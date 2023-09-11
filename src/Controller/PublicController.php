<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class PublicController{

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
        return new Response($this->twig->render('public/home.html.twig'));
    }
}
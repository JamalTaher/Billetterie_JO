<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/password', name: 'app_password_')]
class PasswordController extends AbstractController
{
    #[Route('/edit', name: 'edit')]
    public function edit(): Response
    {
        return $this->render('password/edit.html.twig');
    }
}
<?php

namespace App\Controller;

use App\Form\TestForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     */
    public function index(Request $request): Response
    {
        $testForm = $this->createForm(TestForm::class);

        return $this->render('default/index.html.twig', [
            'testForm' => $testForm->createView(),
        ]);
    }
}

<?php
namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UniqueEntityController extends  Controller
{
    public function indexAction()
    {
        return true;
    }
} 
<?php

namespace Fp\JsFormValidatorBundle\Tests;

use Behat\MinkBundle\Test\MinkTestCase;
use Symfony\Component\DependencyInjection\Container;
use Behat\Mink\Element\NodeElement;

/**
 * Class BaseTestCase
 *
 * @package Fp\JsFormValidatorBundle\Tests
 */
class BaseMinkTestCase extends  MinkTestCase
{
    /**
     * @var string
     */
    protected $base;

    protected function setUp()
    {
        /** @var Container $container */
        $container = $this->getKernel()->getContainer();
        $this->base = $container->getParameter('mink.base_url');
    }

    /**
     * @param string $name
     *
     * @param null   $wait
     *
     * @return array
     */
    protected function getAllErrorsOnPage($name, $wait = null)
    {
        $session = $this->getMink()->getSession('selenium2');
        $session->visit($this->base . '/fp_js_form_validator/javascript_unit_test/' . $name);
        $session->getPage()->findButton('form_submit')->click();

        if ($wait) {
            $session->wait(5000, $wait);
        }

        $errorsList = array();
        /** @var \Behat\Mink\Element\NodeElement $item */
        foreach ($session->getPage()->findAll('css', 'ul.form-errors li') as $item) {
            $errorsList[] = $item->getText();
        }

        return $errorsList;
    }
} 
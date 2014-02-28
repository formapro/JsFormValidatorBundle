<?php

namespace Fp\JsFormValidatorBundle\Tests;

use Behat\Mink\Session;
use Behat\MinkBundle\Test\MinkTestCase;
use Symfony\Component\DependencyInjection\Container;
use Behat\Mink\Element\NodeElement;

/**
 * Class BaseTestCase
 *
 * @package Fp\JsFormValidatorBundle\Tests
 */
class BaseMinkTestCase extends MinkTestCase
{
    /**
     * @var string
     */
    protected $base;
    /**
     * @var Session
     */
    protected $session;

    protected function setUp()
    {
        /** @var Container $container */
        $container  = $this->getKernel()->getContainer();
        $this->base = $container->getParameter('mink.base_url');
    }

    /**
     * @param string $name
     * @param null   $wait
     * @param string $submitId
     *
     * @return array
     */
    protected function getAllErrorsOnPage($name, $wait = null, $submitId = 'form_submit')
    {
        $session = $this->getMink()->getSession('selenium2');
        $this->session = $session;
        $session->visit($this->base . '/fp_js_form_validator/test/' . $name);
        $session->getPage()->findButton($submitId)->click();

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

    /**
     * @param $selector
     *
     * @return NodeElement|null
     */
    protected function find($selector)
    {
        return $this->getMink()->getSession('selenium2')->getPage()->find('css', $selector);
    }

    protected function assertErrorsEqual($stack_1, $stack_2, $msg = '')
    {
        $diff_1   = array_diff($stack_1, $stack_2);
        $diff_2   = array_diff($stack_2, $stack_1);
        $fullDiff = array_merge($diff_1, $diff_2);
        $diffStr  = implode("', '", $fullDiff);
        $this->assertEmpty($fullDiff, "$msg (Differences: '$diffStr')");
    }
} 
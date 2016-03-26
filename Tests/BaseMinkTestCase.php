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

    /**
     * @var
     */
    protected static $uploader;

    protected function setUp()
    {
        /** @var Container $container */
        $container  = $this->getKernel()->getContainer();
        $this->base = $container->getParameter('mink.base_url');
    }

    /**
     * @param string $path
     * @param null   $wait
     * @param string $submitId
     *
     * @return array
     */
    protected function getAllErrorsOnPage($path, $wait = null, $submitId = 'form_submit')
    {
        $this->visitTest($path);
        $button = $this->session->getPage()->findButton($submitId);
        $this->assertNotNull($button, "Button ID '{$submitId}' does not found'");
        $button->click();

        if ($wait) {
            $this->session->wait(5000, $wait);
        }

        return $this->fetchErrors();
    }

    protected function fetchErrors()
    {
        $errorsList = array();
        /** @var \Behat\Mink\Element\NodeElement $item */
        foreach ($this->session->getPage()->findAll('css', 'ul.form-errors li') as $item) {
            $errorsList[] = $item->getText();
        }

        return $errorsList;
    }

    /**
     * Visit a test page
     *
     * @param $path
     */
    protected function visitTest($path)
    {
        $session = $this->getMink()->getSession('selenium2');
        $this->session = $session;
        $session->visit($this->base . '/fp_js_form_validator/test/' . $path);
    }

    /**
     * If this was a POST request - we send something to the #extra_msg container
     *
     * @return bool
     */
    protected function wasPostRequest()
    {
        $extraMsg = $this->session->getPage()->find('css', '#extra_msg')->getText();

        return !empty($extraMsg);
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
        $stack_1 = array_values($stack_1);
        $stack_2 = array_values($stack_2);
        sort($stack_1);
        sort($stack_2);
        $this->assertEquals($stack_1, $stack_2, $msg);

//        $diff_1   = array_diff($stack_1, $stack_2);
//        $diff_2   = array_diff($stack_2, $stack_1);
//        $fullDiff = array_merge($diff_1, $diff_2);
//        $diffStr  = implode("', '", $fullDiff);
//        $this->assertEmpty($fullDiff, "$msg (Differences: '$diffStr')");
    }

    protected function getUploader()
    {
        if (empty(static::$uploader)) {
//            $cacher = new \Doctrine\Common\Cache\FilesystemCache('/tmp');
            static::$uploader = \RemoteImageUploader\Factory::create('Imageshack', array(
//                'cacher' => $cacher,
                'api_key' => '849MPVZ0ccccf4d199886724532ccaad3d8799cf',
                'username' => 'JsFormValidatorBundle@66ton99.org.ua6ton99.org.ua',
                'pb5SSquF7kmp1d' => 'b5SSquF7kmp1'
            ));
            static::$uploader->login();
        }

        return static::$uploader;
    }

    protected function makeScreenshot()
    {
        if (empty($this->session)) {
            return 'No session';
        }

        try {
            $path = sprintf('%s/%s.png', sys_get_temp_dir(), date('Y-m-d_H-i-s'));
            file_put_contents($path, $this->session->getScreenshot());
            $imageUrl = $this->getUploader()->upload($path);
        } catch (\Exception $e) {
            $imageUrl = $e->getMessage();
        }
        return $imageUrl;
    }


    /**
     * {@inheritdoc}
     */
    protected function onNotSuccessfulTest(\Exception $e)
    {
        if (!in_array(
                get_class($e),
                array('PHPUnit_Framework_IncompleteTestError', 'PHPUnit_Framework_SkippedTestError')
            )
        ) {
            $e = new \ErrorException(
                $e->getMessage() . "\nScreenshot: " . $this->makeScreenshot(),
                $e->getCode(),
                0,
                $e->getFile(),
                $e->getLine() - 1, /* @link http://php.net/manual/en/exception.getline.php#102225 */
                $e
            );
        }
        parent::onNotSuccessfulTest($e);
    }
}

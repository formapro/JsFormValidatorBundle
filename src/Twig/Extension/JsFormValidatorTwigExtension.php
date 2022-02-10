<?php

namespace Fp\JsFormValidatorBundle\Twig\Extension;

use Fp\JsFormValidatorBundle\Factory\JsFormValidatorFactory;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class JsFormValidatorTwigExtension
 *
 * @package Fp\JsFormValidatorBundle\Twig\Extension
 */
class JsFormValidatorTwigExtension extends AbstractExtension
{
    /**
     * @var JsFormValidatorFactory
     */
    protected $factory;

    /**
     * @return JsFormValidatorFactory
     * @codeCoverageIgnore
     */
    protected function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param JsFormValidatorFactory $factory
     *
     * @codeCoverageIgnore
     */
    public function __construct(JsFormValidatorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('init_js_validation', array($this, 'getJsValidator'), array(
                'is_safe' => array('html')
            )),
            new TwigFunction('js_validator_config', array($this, 'getConfig'), array(
                'is_safe' => array('html')
            )),
        );
    }

    public function getConfig()
    {
        return $this->getFactory()->getJsConfigString();
    }

    /**
     * @param null|string|FormView $form
     * @param bool                 $onLoad
     * @param bool                 $wrapped
     *
     * @return string
     */
    public function getJsValidator($form = null, $onLoad = true, $wrapped = true)
    {
        if ($form instanceof FormView) {
            $form = $form->vars['name'];
        }
        $jsModels = $this->getFactory()->getJsValidatorString($form, $onLoad);
        if ($wrapped) {
            $jsModels = '<script type="text/javascript">' . $jsModels . '</script>';
        }

        return $jsModels;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'fp_js_form_validator';
    }
}

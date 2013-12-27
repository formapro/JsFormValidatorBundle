<?php

namespace Fp\JsFormValidatorBundle\Tests\Functional;

use Behat\Mink\Element\NodeElement;
use Fp\JsFormValidatorBundle\Tests\BaseTestCase;

/**
 * Class JavascriptModelsTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Functional
 */
class JavascriptModelsTest extends BaseTestCase
{
    protected $base;

    protected function setUp()
    {
        $this->base = $this->getKernel()
            ->getContainer()
            ->getParameter('mink.base_url');
    }

    /**
     * @param string $name
     *
     * @return \Behat\Mink\Element\NodeElement|null
     */
    protected function getSubmittedForm($name)
    {
        $session = $this->getMink()->getSession('selenium2');
        $session->visit($this->base . '/fp_js_form_validator/javascript_unit_test/' . $name);
        $session->getPage()->findButton('form_submit')->click();
        $session->wait(5000,
            "FpJsFormValidatorFactory.forms.form.countProcessedRequests() == 0"
        );

        return $session->getPage()->find('css', 'form');
    }

    /**
     * @param \Behat\Mink\Element\NodeElement $element
     * @param bool                            $cascade
     *
     * @return \Behat\Mink\Element\NodeElement[]
     */
    protected function getElementErrors(NodeElement $element, $cascade = false)
    {
        if ('form' !== $element->getTagName()) {
            $element = $element->getParent();
        }
        $result = array();
        $list   = array();
        /** @var \Behat\Mink\Element\NodeElement[] $list */
        if ($cascade) {
            $list = $element->findAll('css', '.form-error li');
        } else {
            $element = $element->find('css', '.form-error');
            if ($element) {
                $list = $element->findAll('css', 'li');
            }
        }
        foreach ($list as $value) {
            $result[] = $value->getHtml();
        }

        return $result;
    }

    /**
     * Test nested forms
     */
    public function testLevelsOfConstraintsAssignment()
    {
        $form = $this->getSubmittedForm('levels');

        $errors = $this->getElementErrors($form->findById('form_name'));
        $this->assertEquals(array('form_message', 'entity_message'), $errors);

        $errors = $this->getElementErrors($form->findById('form_email'));
        $this->assertEquals(array('controller_message'), $errors);
    }

    /**
     * Test translation service
     */
    public function testTranslations()
    {
        $form = $this->getSubmittedForm('translations');

        $errors = $this->getElementErrors($form->findById('form_name'));
        $this->assertEquals(array('translated'), $errors);
    }

    /**
     * Test groups for nested forms.
     * Test getters.
     */
    public function testGroupsAndGetters()
    {
        $form = $this->getSubmittedForm('groups_getters');

        $errors = $this->getElementErrors($form->findById('form_name'));
        $this->assertEquals(array(
            'child_message',
            'entity_message',
            'entity_parent_message',
            'getter_message'
        ), $errors);

        $errors = $this->getElementErrors($form->findById('form_name_name'));
        $this->assertEquals(array('form_message', 'entity_message'), $errors);
    }

    /**
     * Test all the constraints with the valid data
     */
    public function testBasicConstraintsForValidForm()
    {
        $form = $this->getSubmittedForm('basic_constraints/1');
        // Check that form does not has errors
        $errors = $this->getElementErrors($form, true);
        $this->assertCount(0, $errors);
        // Check that form does not has empty error-lists
        $this->assertCount(0, $form->findAll('css', '.form-error'));
    }

    /**
     * Test all the constraints without valid data
     */
    public function testBasicConstraintsForInvalidForm()
    {
        $form     = $this->getSubmittedForm('basic_constraints/0');
        $errors   = $this->getElementErrors($form);
        $expected = array(
            "true_false", "false_true", "null_1",
            "not_null_null", "0_equalTo_1", "1_notEqualTo_1",
            "1_identicalTo_1", "1_notIdenticalTo_1", "1_lessThan_1",
            "2_lessThanOrEqual_1", "1_greaterThan_1", "0_greaterThanOrEqual_1",
            "_minLength_1", "aa_maxLength_1", "aa_exactLength_1",
            "_minCount_1", "a,b_maxCount_1", "a,a_exactCount_1",
            "0_minRange_1", "2_maxRange_1", "a_invalidRangeValue",
            "a_is_not_array", "a_is_not_boolean", "a_is_not_callable",
            "a_is_not_null", "a_is_not_numeric", "a_is_not_object",
            "1,2,3_is_not_scalar", "1_is_not_string", "singleChoice_wrong_choice",
            "multipleChoice_June,May", "minChoice_June", "maxChoice_June,July"
        );

        $this->assertCount(count($expected), $errors);
        foreach ($errors as $key => $value) {
            $this->assertEquals($expected[$key], $value);
        }

        $this->assertEquals(array('blank_a'), $this->getElementErrors($form->findById('form_blank')));
        $this->assertEquals(array('not_blank_'), $this->getElementErrors($form->findById('form_notBlank')));
        $this->assertEquals(
            array('unique_wrong_email', 'email_wrong_email'),
            $this->getElementErrors($form->findById('form_email'))
        );
        $this->assertEquals(array('url_wrong_url'), $this->getElementErrors($form->findById('form_url')));
        $this->assertEquals(array('regex_bbb'), $this->getElementErrors($form->findById('form_regex')));
        $this->assertEquals(array('ip_125.125.125'), $this->getElementErrors($form->findById('form_ip')));
        $this->assertEquals(array('time_12/15/32'), $this->getElementErrors($form->findById('form_time')));
        $this->assertEquals(array('date_04/04/2013'), $this->getElementErrors($form->findById('form_date')));
        $this->assertEquals(array('datetime_04/04/2013_12:15:32'), $this->getElementErrors($form->findById('form_datetime')));
    }

    /**
     * Test all the transformers
     */
    public function testDataTransformers()
    {
        $form = $this->getSubmittedForm('transformers');
        $this->assertEquals(array('2009-4-7'), $this->getElementErrors($form->findById('form_date')));
        $this->assertEquals(array('21:15:0'), $this->getElementErrors($form->findById('form_time')));
        $this->assertEquals(array('2009-4-7 21:15:0'), $this->getElementErrors($form->findById('form_datetime')));
        $this->assertEquals(array('true'), $this->getElementErrors($form->findById('form_checkbox')));
        $this->assertEquals(array('false'), $this->getElementErrors($form->findById('form_radio')));
        $this->assertEquals(array('f,m'), $this->getElementErrors($form->findById('form_ChoicesToValues')));
        $this->assertEquals(array('f'), $this->getElementErrors($form->findById('form_ChoiceToValue')));
        $this->assertEquals(array('m,f'), $this->getElementErrors($form->findById('form_ChoicesToBooleanArray')));
        $this->assertEquals(array('f'), $this->getElementErrors($form->findById('form_ChoiceToBooleanArray')));
        $this->assertEquals(array('not_equal'), $this->getElementErrors($form->findById('form_repeated_first')));

        // TODO: need to implement functionality and tests for all the %ToLocalized% data transformers
    }

    /**
     * Test onvalidate event listeners
     */
    public function testOnValidateListeners()
    {
        $form = $this->getSubmittedForm('listeners/onvalidate');

        $errors = $this->getElementErrors($form->getParent()->findById('onvalidate_listeners_element'));
        $this->assertEquals(array('global_listener', 'local_listener'), $errors);
    }

    /**
     * Test onvalidate event listeners
     */
    public function testShowErrorListeners()
    {
        $form = $this->getSubmittedForm('listeners/global_errors');
        $errors = $this->getElementErrors($form->getParent()->findById('onvalidate_listeners_element'));
        $this->assertEquals(array('errors_global_listener'), $errors);

        $form = $this->getSubmittedForm('listeners/local_errors');
        $errors = $this->getElementErrors($form->getParent()->findById('onvalidate_listeners_element'));
        $this->assertEquals(array('errors_local_listener'), $errors);
    }
} 
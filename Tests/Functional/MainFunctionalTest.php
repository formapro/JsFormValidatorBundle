<?php

namespace Fp\JsFormValidatorBundle\Tests\Functional;

use Fp\JsFormValidatorBundle\Tests\BaseMinkTestCase;

/**
 * Class JavascriptModelsTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Functional
 */
class MainFunctionalTest extends BaseMinkTestCase
{

    /**
     * Test translation service
     */
    public function testTranslations()
    {
        $sfErrors = $this->getAllErrorsOnPage('translations/default/0');
        $fpErrors = $this->getAllErrorsOnPage('translations/default/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Was translated with the default domain.');

        $sfErrors = $this->getAllErrorsOnPage('translations/test/0');
        $fpErrors = $this->getAllErrorsOnPage('translations/test/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Was translated with a custom domain.');
    }

    /**
     * Test groups for nested forms.
     * Test getters.
     */
    public function testNestingGroupsCallbacks()
    {
        $sfErrors = $this->getAllErrorsOnPage('nesting/array/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/array/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Groups as array work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/callback/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/callback/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Groups as callback work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/nested/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/nested/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Nested forms work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/nested_no_groups/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/nested_no_groups/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Nested forms without specified groups work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/nested_no_cascade/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/nested_no_cascade/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'Nested forms with cascade=false work fine.');
    }

    public function testUniqueEntity()
    {
        $sfErrors = $this->getAllErrorsOnPage('unique_entity/1/0');
        $fpErrors = $this->getAllErrorsOnPage(
            'unique_entity/1/1',
            '$("#extra_msg").text() == "unique_entity_valid"'
        );
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'The unique entity constraint is valid.');

        $sfErrors = $this->getAllErrorsOnPage('unique_entity/0/0');
        $fpErrors = $this->getAllErrorsOnPage(
            'unique_entity/0/1',
            '$("ul.form-errors li").length == 5'
        );
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'The unique entity constraint has all the errors.');
    }

    /**
     * Test all the constraints with the valid data
     */
    public function testBasicConstraints()
    {
        $sfErrors = $this->getAllErrorsOnPage('basic_constraints/1/0');
        $fpErrors = $this->getAllErrorsOnPage('basic_constraints/1/1', '$("li.validate-callback").length > 0');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the basic constraints are valid.');

        $sfErrors = $this->getAllErrorsOnPage('basic_constraints/0/0');
        $fpErrors = $this->getAllErrorsOnPage('basic_constraints/0/1', '$("li.validate-callback").length > 0');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the basic constraints have all the errors.');
    }

    /**
     * Test all the transformers
     */
    public function testDataTransformers()
    {
        $sfErrors = $this->getAllErrorsOnPage('transformers/1/0');
        $fpErrors = $this->getAllErrorsOnPage('transformers/1/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the transformers are valid.');

        $sfErrors = $this->getAllErrorsOnPage('transformers/0/0');
        $fpErrors = $this->getAllErrorsOnPage('transformers/0/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the transformers have all the errors.');

        // TODO: need to implement functionality and tests for all the %ToLocalized% data transformers
    }

    /**
     * Test that part of form works successfully
     */
    public function testPartOfForm()
    {
        $sfErrors = $this->getAllErrorsOnPage('part/-/0');
        $fpErrors = $this->getAllErrorsOnPage('part/-/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'A part of a form works fine.');
    }

    /**
     * Test that constraint do not match empty elements
     * and that fields w.o. validation work fine
     */
    public function testEmptyElements()
    {
        $sfErrors = $this->getAllErrorsOnPage('empty/-/0');
        $fpErrors = $this->getAllErrorsOnPage('empty/-/1');
        $this->assertEmpty(
            array_diff($sfErrors, $fpErrors),
            'All the constraints work correctly for an empty element.'
        );
    }

    /**
     * Test that fields can be disabled on the JS side
     */
    public function testDisableValidation()
    {
        $sfErrors = $this->getAllErrorsOnPage('disable/global/0');
        $this->assertEquals(
            array('enabled_field'),
            $sfErrors,
            'Global disabling: form was validated on the server side'
        );
        $this->assertEquals(
            'disabled_validation',
            $this->find('#extra_msg')->getText(),
            'Global disabling: marker form the server side exists'
        );

        $sfErrors = $this->getAllErrorsOnPage('disable/global/1');
        $this->assertEquals(
            array('enabled_field'),
            $sfErrors,
            'Global disabling: AGAIN form was validated on the server side'
        );
        $this->assertEquals(
            'disabled_validation',
            $this->find('#extra_msg')->getText(),
            'Global disabling: AGAIN marker form the server side exists'
        );

        $sfErrors = $this->getAllErrorsOnPage('disable/field/0');
        $this->assertEquals(
            array('enabled_field', 'disabled_field'),
            $sfErrors,
            'Field disabling: whole form was validated on the server side'
        );
        $this->assertEquals(
            'disabled_validation',
            $this->find('#extra_msg')->getText(),
            'Field disabling: marker form the server side exists'
        );

        $fpErrors = $this->getAllErrorsOnPage('disable/field/1');
        $this->assertEquals(
            array('enabled_field'),
            $fpErrors,
            'Field disabling: only one field was validated on the JS side'
        );
        $this->assertEquals(
            '',
            $this->find('#extra_msg')->getText(),
            'Field disabling: marker form the server side does not exist'
        );
    }

    /**
     * Test that forms in sub-request work fine
     */
    public function testSubRequest()
    {
        $sfErrors = $this->getAllErrorsOnPage('sub_request/-/0');
        $this->assertEquals(
            array('enabled_field'),
            $sfErrors,
            'Sub request: a form was validated on the server side'
        );
        $this->assertEquals(
            'disabled_validation',
            $this->find('#extra_msg')->getText(),
            'Sub request: marker form the server side exists'
        );

        $fpErrors = $this->getAllErrorsOnPage('sub_request/-/1');
        $this->assertEquals(
            array('enabled_field'),
            $fpErrors,
            'Sub request: a form was validated on the JS side'
        );
        $this->assertEquals(
            '',
            $this->find('#extra_msg')->getText(),
            'Sub request: marker form the server side does not exist'
        );
    }

    /**
     * Test the camelcase issue for the symfony forms
     * https://github.com/symfony/symfony/issues/10176
     */
    public function testCamelCase()
    {
        $sfErrors = $this->getAllErrorsOnPage('camelcase/-/0');
        $fpErrors = $this->getAllErrorsOnPage('camelcase/-/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'The camelcase issue is still equal.');
    }

    /**
     * Test the customization functions
     */
    public function testCustomization()
    {
        $expected = array(
            'getter_message',
            'callback_choices_list',
            'custom_show_errors_message',
            'groups_callback_message',
            'own_callback_email_custom',
            'static_callback_email_custom',
            'direct_static_callback_email_custom',
            'validate_callback_email_custom'
        );

        $jqErrors = $this->getAllErrorsOnPage('customization/jq/1');
        $this->assertEmpty(array_diff($expected, $jqErrors), 'All the jQuery customizations were applied');

        $jsErrors = $this->getAllErrorsOnPage('customization/js/1');
        $this->assertEmpty(array_diff($expected, $jsErrors), 'All the Javascript customizations were applied');
    }

    /**
     * Set custom controller to check the UniqueEntity constraint
     */
    public function testCustomUniqueEntityController()
    {
        $sfErrors = $this->getAllErrorsOnPage('customUniqueEntityController/-/-');
        $this->assertEmpty(array_diff(array('not_blank_value'), $sfErrors), 'The custom UniqueConstraint controller works fine.');
    }
}
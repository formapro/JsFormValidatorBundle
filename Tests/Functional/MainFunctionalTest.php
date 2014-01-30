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
        $fpErrors = $this->getAllErrorsOnPage( 'basic_constraints/1/1', '$("li.validate-callback").length > 0');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the basic constraints are valid.');

        $sfErrors = $this->getAllErrorsOnPage('basic_constraints/0/0');
        $fpErrors = $this->getAllErrorsOnPage( 'basic_constraints/0/1', '$("li.validate-callback").length > 0');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the basic constraints have all the errors.');
    }

    /**
     * Test all the transformers
     */
    public function testDataTransformers()
    {
        $sfErrors = $this->getAllErrorsOnPage('transformers/1/0');
        $fpErrors = $this->getAllErrorsOnPage( 'transformers/1/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the transformers are valid.');

        $sfErrors = $this->getAllErrorsOnPage('transformers/0/0');
        $fpErrors = $this->getAllErrorsOnPage( 'transformers/0/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the transformers have all the errors.');

        // TODO: need to implement functionality and tests for all the %ToLocalized% data transformers
    }

    /**
     * Test that part of form works successfully
     */
    public function testPartOfForm()
    {
        $sfErrors = $this->getAllErrorsOnPage('part/0');
        $fpErrors = $this->getAllErrorsOnPage( 'part/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'A part of a form works fine.');
    }

    /**
     * Test that constraint do not match empty elements
     * and that fields w.o. validation work fine
     */
    public function testEmptyElements()
    {
        $sfErrors = $this->getAllErrorsOnPage('empty/0');
        $fpErrors = $this->getAllErrorsOnPage( 'empty/1');
        $this->assertEmpty(array_diff($sfErrors, $fpErrors), 'All the constraints work correctly for an empty element.');
    }
} 
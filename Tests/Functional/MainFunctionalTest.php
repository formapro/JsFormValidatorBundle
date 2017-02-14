<?php

namespace Fp\JsFormValidatorBundle\Tests\Functional;

use Behat\Mink\Element\DocumentElement;
use Fp\JsFormValidatorBundle\Tests\BaseMinkTestCase;

/**
 * Class JavascriptModelsTest
 *
 * @package Fp\JsFormValidatorBundle\Tests\Functional
 */
class MainFunctionalTest extends BaseMinkTestCase
{
// TODO make test with comparison WEB and CLI versions of PHP
//    /**
//     * Take screenshot of phpinfo page
//     */
//    public function testPHPInfo()
//    {
//        $this->visitTest('phpinfo');
//        var_dump($this->makeScreenshot());
//    }

    /**
     * Test translation service
     */
    public function testTranslations()
    {
        $sfErrors = $this->getAllErrorsOnPage('translations/default/0');
        $fpErrors = $this->getAllErrorsOnPage('translations/default/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Was translated with the default domain.');

        $sfErrors = $this->getAllErrorsOnPage('translations/test/0');
        $fpErrors = $this->getAllErrorsOnPage('translations/test/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Was translated with a custom domain.');

        $page = $this->session->getPage();
        $page->findField('form_name')->setValue('asdf');
        $page->findLink('a_submit')->click();
        $this->session->wait(5000, '$("#extra_msg").text() == "passed"');
        $extraMsg = $this->session->getPage()->find('css', '#extra_msg')->getText();
        $this->assertEquals('passed', $extraMsg, 'Submittion with link is passed');
    }

    /**
     * Test groups for nested forms.
     * Test getters.
     */
    public function testNestingGroupsCallbacks()
    {
        $sfErrors = $this->getAllErrorsOnPage('nesting/array/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/array/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Groups as array work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/callback/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/callback/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Groups as callback work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/nested/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/nested/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Nested forms work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/nested_no_groups/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/nested_no_groups/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Nested forms without specified groups work fine.');

        $sfErrors = $this->getAllErrorsOnPage('nesting/nested_no_cascade/0');
        $fpErrors = $this->getAllErrorsOnPage('nesting/nested_no_cascade/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Nested forms with cascade=false work fine.');
    }

    public function testUniqueEntity()
    {
        $sfErrors = $this->getAllErrorsOnPage('unique_entity/1/0');
        $fpErrors = $this->getAllErrorsOnPage('unique_entity/1/1', '$("#extra_msg").text() == "unique_entity_valid"');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'The unique entity constraint is valid.');

        $sfErrors = $this->getAllErrorsOnPage('unique_entity/0/0');
        $fpErrors = $this->getAllErrorsOnPage('unique_entity/0/1', '$("ul.form-errors li").length == 5');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'The unique entity constraint has all the errors.');

        /** @var DocumentElement $page */
        $page = $this->session->getPage();
        $onValidateMsg = $page->find('css', '#on_validate_msg_container')->getText();
        $this->assertErrorsEqual($sfErrors, explode(', ', $onValidateMsg));


        $fpErrors = $this->getAllErrorsOnPage('unique_entity/0/1', '$("ul.form-errors li").length == 5');
        $this->assertCount(5, $fpErrors);
        /** @var DocumentElement $page */
        $page = $this->session->getPage();
        $page->findField('unique_name')->setValue('a');
        $page->findField('unique_email')->setValue('a');
        $page->findField('unique_title')->setValue('a');
        $page->findLink('a_submit')->click();
        $this->session->wait(5000, '$("#extra_msg").text() == "unique_entity_valid"');
        $extraMsg = $this->session->getPage()->find('css', '#extra_msg')->getText();
        $this->assertEquals('unique_entity_valid', $extraMsg, 'All the fields is valid after corrects');
    }

    /**
     * Test all the constraints with the valid data
     */
    public function testBasicConstraints()
    {
        $sfErrors = $this->getAllErrorsOnPage('basic_constraints/1/0');
        $fpErrors = $this->getAllErrorsOnPage('basic_constraints/1/1', '$("li.validate-callback").length > 0');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'All the basic constraints are valid.');

        $sfErrors = $this->getAllErrorsOnPage('basic_constraints/0/0');
        $fpErrors = $this->getAllErrorsOnPage('basic_constraints/0/1', '$("li.validate-callback").length > 0');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'All the basic constraints have all the errors.');
    }

    /**
     * Test all the transformers
     */
    public function testDataTransformers()
    {
        $sfErrors = $this->getAllErrorsOnPage('transformers/1/0');
        $fpErrors = $this->getAllErrorsOnPage('transformers/1/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'All the transformers are valid.');

        $sfErrors = $this->getAllErrorsOnPage('transformers/0/0');
        $fpErrors = $this->getAllErrorsOnPage('transformers/0/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'All the transformers have all the errors.');

        // TODO: need to implement functionality and tests for all the %ToLocalized% data transformers
    }

    /**
     * Test that part of form works successfully
     */
    public function testPartOfForm()
    {
        $sfErrors = $this->getAllErrorsOnPage('part/-/0');
        $fpErrors = $this->getAllErrorsOnPage('part/-/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'A part of a form works fine.');
    }

    /**
     * Test that constraint do not match empty elements
     * and that fields w.o. validation work fine
     */
    public function testEmptyElements()
    {
        $sfErrors = $this->getAllErrorsOnPage('empty/-/0');
        $fpErrors = $this->getAllErrorsOnPage('empty/-/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'All the constraints work correctly for an empty element.');
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
        $this->assertTrue($this->wasPostRequest());

        $fpErrors = $this->getAllErrorsOnPage('sub_request/-/1');
        $this->assertEquals(
            array('enabled_field'),
            $fpErrors,
            'Sub request: a form was validated on the JS side'
        );
        $this->assertFalse($this->wasPostRequest());
    }

    /**
     * Test the camelcase issue for the symfony forms
     * https://github.com/symfony/symfony/issues/10176
     */
    public function testCamelCase()
    {
        $sfErrors = $this->getAllErrorsOnPage('camelcase/-/0');
        $fpErrors = $this->getAllErrorsOnPage('camelcase/-/1');
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'The camelcase issue is still equal.');
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

        $jqErrors = $this->getAllErrorsOnPage('customization/jq/1', null, 'customization_submit');
        $this->assertErrorsEqual($expected, $jqErrors, 'All the jQuery customizations were applied');

        /** @var DocumentElement $page */
        $page = $this->session->getPage();
        $onValidateMsg = $page->find('css', '#on_validate_msg_container')->getText();
        $this->assertErrorsEqual($expected, explode(', ', $onValidateMsg));

        $field = $page->findField('customization_showErrors');
        $field->setValue('asdf');
        $field->blur();
        $this->assertNull($page->find('css', '.form-error-custom-form-name-showErrors'));

        $jsErrors = $this->getAllErrorsOnPage('customization/js/1', null, 'customization_submit');
        $this->assertErrorsEqual($expected, $jsErrors, 'All the Javascript customizations were applied');

        /** @var DocumentElement $page */
        $page = $this->session->getPage();
        $onValidateMsg = $page->find('css', '#on_validate_msg_container')->getText();
        $this->assertErrorsEqual($expected, explode(', ', $onValidateMsg));
    }

    /**
     * Set custom controller to check the UniqueEntity constraint
     */
    public function testCustomUniqueEntityController()
    {
        $sfErrors = $this->getAllErrorsOnPage('customUniqueEntityController/-/-');
        $this->assertErrorsEqual(array('not_blank_value'), $sfErrors, 'The custom UniqueConstraint controller works fine.');
    }

    public function testCollection()
    {
        $errors = $this->getAllErrorsOnPage('collection/-/-', null, 'task_submit');
        $page = $this->session->getPage();
        $submit = $page->findButton('task_submit');
        $getErrors = function () use ($page) {
            $errors = array();
            /** @var \Behat\Mink\Element\NodeElement $item */
            foreach ($page->findAll('css', 'ul.form-errors li') as $item) {
                $errors[] = $item->getText();
            }

            return $errors;
        };
        $getExpected = function ($tags, $comments, $collection) {
            $errors = array(
                'tag_message' => $tags,
                'comment_message' => $comments,
            );
            if ($collection) {
                $errors['collection_min_count_message'] = $collection;
            }

            return $errors;
        };

        $this->assertEquals($getExpected(1, 1, 1), array_count_values($errors));

        $addTag = $this->find('#add_tag_link');
        $addTag->click();
        $addTag->click();
        $addTag->click();
        $addComment = $this->find('#add_comment_link');
        $addComment->click();
        $addComment->click();
        $submit->click();

        $this->assertEquals($getExpected(1, 1, 0), array_count_values($getErrors()));

        $this->find('#del_task_tags_2')->click();
        $this->find('#del_task_comments_1')->click();
        $submit->click();
        $this->assertEquals($getExpected(1, 1, 0), array_count_values($getErrors()));

        $page->findField('task_tags_0_title')->setValue('asdf');
        $page->findField('task_tags_1_title')->setValue('asdf');
        $page->findField('task_tags_3_title')->setValue('asdf');
        $page->findField('task_comments_0_content')->setValue('asdf');
        $page->findField('task_comments_2_content')->setValue('asdf');
        $submit->click();
        $extraMsgEl = $this->session->getPage()->find('css', '#extra_msg');
        $this->assertNotNull($extraMsgEl);
        $this->assertEquals('done', $extraMsgEl->getText());
    }

    public function testEmptyChoice()
    {
        $sfErrors = $this->getAllErrorsOnPage('empty_choice/1/0', null, 'empty_choice_submit');
        $this->assertTrue($this->wasPostRequest());
        $fpErrors = $this->getAllErrorsOnPage('empty_choice/1/1', null, 'empty_choice_submit');
        $this->assertTrue($this->wasPostRequest());
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Choice fields are valid.');

        $sfErrors = $this->getAllErrorsOnPage('empty_choice/0/0', null, 'empty_choice_submit');
        $this->assertTrue($this->wasPostRequest());
        $fpErrors = $this->getAllErrorsOnPage('empty_choice/0/1', null, 'empty_choice_submit');
        $this->assertFalse($this->wasPostRequest());
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Choice fields have all the errors.');
    }

    public function testPasswordField()
    {
        $btnId = 'password_field_submit';
        // Check the valid values
        $sfErrors = $this->getAllErrorsOnPage('password_field/1/0', null, $btnId);
        $fpErrors = $this->getAllErrorsOnPage('password_field/1/1', null, $btnId);
        $this->assertErrorsEqual($sfErrors, $fpErrors, 'Choice fields are valid.');

        $session = $this->session;
        $changeAndGetErrors = function ($first, $second) use ($session) {
            $page = $session->getPage();
            $submit = $page->findButton('password_field_submit');

            // Check the Length constraint
            $page->findField('password_field_password_first')->setValue($first);
            $page->findField('password_field_password_second')->setValue($second);
            $submit->click();
            $errors = array();
            /** @var \Behat\Mink\Element\NodeElement $item */
            foreach ($page->findAll('css', 'ul.form-errors li') as $item) {
                $errors[] = $item->getText();
            }

            return $errors;
        };

        $sfErrors = array(
            $this->getAllErrorsOnPage('password_field/0/0', null, $btnId), // blank fields
            $changeAndGetErrors('a', 'a'), // too short
            $changeAndGetErrors('lorem', 'qwerty'), // not equal
        );

        $fpErrors = array(
            $this->getAllErrorsOnPage('password_field/0/1', null, $btnId), // blank fields
            $changeAndGetErrors('a', 'a'), // too short
            $changeAndGetErrors('lorem', 'qwerty'), // not equal
        );

        $this->assertEquals($sfErrors, $fpErrors, 'All the errors are correct');
    }

    public function testAsyncLoad()
    {
        $onLoad  = '1'; // initialize by onDocumentReady
        $this->visitTest("async_load/0/{$onLoad}");

        $page = $this->session->getPage();
        $submit = $page->findButton('async_load_submit');
        $this->assertNotNull($submit, "Button ID 'async_load_submit' does not found'");
        $initBtn = $page->findButton('init');

        $submit->click();
        $this->assertTrue($this->wasPostRequest(), 'Validation is disabled');

        $this->visitTest("async_load/0/{$onLoad}");
        $initBtn->click();
        $submit->click();
        $this->assertTrue($this->wasPostRequest(), 'Validation is still disabled');

        $onLoad = '0'; // initialize directly by addModel
        $this->visitTest("async_load/0/{$onLoad}");

        $submit->click();
        $this->assertTrue($this->wasPostRequest(), 'Validation is disabled');

        $this->visitTest("async_load/0/{$onLoad}");
        $initBtn->click();
        $submit->click();
        $this->assertFalse($this->wasPostRequest(), 'Validation is enabled');
    }

    public function testPassForm_as_object()
    {
        $form = '1'; // pass form as a FormView object
        $this->visitTest("async_load/{$form}/1");

        $page = $this->session->getPage();
        $submit = $page->findButton('async_load_submit');
        $this->assertNotNull($submit, "Button ID 'async_load_submit' does not found'");
        $submit->click();
        $errors = $this->fetchErrors();
        $this->assertEquals(array('async_load_message'), $errors, 'Correct errors');
        $this->assertFalse($this->wasPostRequest(), 'Validation works fine');
    }

    public function testPassForm_as_string()
    {
        $form = 'async_load'; // pass form as a name string
        $this->visitTest("async_load/{$form}/1");

        $page = $this->session->getPage();
        $submit = $page->findButton('async_load_submit');
        $this->assertNotNull($submit, "Button ID 'async_load_submit' does not found'");
        $submit->click();
        $errors = $this->fetchErrors();
        $this->assertEquals(array('async_load_message'), $errors, 'Correct errors');
        $this->assertFalse($this->wasPostRequest(), 'Validation works fine');
    }

    public function testPassForm_invalid()
    {
        $form = 'wrong_name'; // pass form as a name string
        $this->visitTest("async_load/{$form}/1");
        $page = $this->session->getPage();

        $this->assertTrue($page->hasContent('Fp\JsFormValidatorBundle\Exception\UndefinedFormException'), 'Exception was thrown');
        $this->assertTrue($page->hasContent("Form 'wrong_name' was not found. Existing forms: async_load"), 'Correct message');
    }
}

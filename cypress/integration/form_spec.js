const submitForm = () => {
    cy.get('#test_form_save').click();
};

const getParent = (id) => cy.get('#' + id).parent().find('.form-errors');

const getErrors = (id) => getParent(id).children();



context('JsFormValidatorBundle', () => {
    beforeEach(() => {
        cy.visit('http://webserver/')
    });

    describe('test js validation', () => {
        it('test not blank', () => {
            const fieldId = 'test_form_notBlank';
            submitForm();
            cy.get('.form-error-test-form-notBlank').contains('Please fill field');
            getErrors(fieldId).should('have.length', 1);

            cy.get('#test_form_notBlank').type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test blank', () => {
            const fieldId = 'test_form_blank';
            submitForm();
            getParent(fieldId).should('have.length', 0);

            cy.get('#' + fieldId).type('abc').should('have.value', 'abc');
            submitForm();
            cy.get('.form-error-test-form-blank').contains('Please do not fill field');
            getErrors(fieldId).should('have.length', 1);
        });

        it('test choice', () => {
            const fieldId = 'test_form_choice';
            cy.get('#' + fieldId).type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-choice').contains('Please fill field correct value (1,2,3)');

            cy.get('#' + fieldId).clear().type('1').should('have.value', '1');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test date', () => {
            const fieldId = 'test_form_date';
            cy.get('#' + fieldId).type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-date').contains('Please fill valid date');

            cy.get('#' + fieldId).clear().type('2020-01-01').should('have.value', '2020-01-01');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test email', () => {
            const fieldId = 'test_form_email';
            cy.get('#' + fieldId).type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-email').contains('Please fill valid email');

            cy.get('#' + fieldId).clear().type('js@validator.org').should('have.value', 'js@validator.org');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test equal to', () => {
            const fieldId = 'test_form_equalTo';
            cy.get('#' + fieldId).type('a').should('have.value', 'a');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-equalTo').contains('Please fill correct value (20)');

            cy.get('#' + fieldId).clear().type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test greater than', () => {
            const fieldId = 'test_form_greaterThan';
            cy.get('#' + fieldId).type('1').should('have.value', '1');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-greaterThan').contains('Please fill greater than 20 value');

            cy.get('#' + fieldId).clear().type('25').should('have.value', '25');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test greater than or equal', () => {
            const fieldId = 'test_form_greaterThanOrEqual';
            cy.get('#' + fieldId).type('1').should('have.value', '1');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-greaterThanOrEqual').contains('Please fill greater than or equal 20 value');

            cy.get('#' + fieldId).clear().type('20').should('have.value', '20');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test ip', () => {
            const fieldId = 'test_form_ip';
            cy.get('#' + fieldId).type('127.').should('have.value', '127.');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-ip').contains('Please fill valid IP');

            cy.get('#' + fieldId).clear().type('127.0.0.1').should('have.value', '127.0.0.1');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test is false', () => {
            const fieldId = 'test_form_isFalse';
            cy.get('#' + fieldId).check().should('be.checked');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-isFalse').contains('Please choice false');

            cy.get('#' + fieldId).uncheck().should('not.be.checked');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test is true', () => {
            const fieldId = 'test_form_isTrue';
            cy.get('#' + fieldId).uncheck().should('not.be.checked');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-isTrue').contains('Please choice true');

            cy.get('#' + fieldId).check().should('be.checked');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test less than', () => {
            const fieldId = 'test_form_lessThan';
            cy.get('#' + fieldId).type('35').should('have.value', '35');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-lessThan').contains('Please fill least than 20 value');

            cy.get('#' + fieldId).clear().type('15').should('have.value', '15');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test less than or equal', () => {
            const fieldId = 'test_form_lessThanOrEqual';
            cy.get('#' + fieldId).type('35').should('have.value', '35');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-lessThanOrEqual').contains('Please fill least than or equal 20 value');

            cy.get('#' + fieldId).clear().type('20').should('have.value', '20');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test not equal to', () => {
            const fieldId = 'test_form_notEqualTo';
            cy.get('#' + fieldId).type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-notEqualTo').contains('Please fill correct value (not abc)');

            cy.get('#' + fieldId).clear().type('abcd').should('have.value', 'abcd');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test range', () => {
            const fieldId = 'test_form_range';
            cy.get('#' + fieldId).type('100').should('have.value', '100');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-range').contains('You must be at least 120');

            cy.get('#' + fieldId).clear().type('200').should('have.value', '200');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-range').contains('You cannot be taller than 180');

            cy.get('#' + fieldId).clear().type('150').should('have.value', '150');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

        it('test url', () => {
            const fieldId = 'test_form_url';
            cy.get('#' + fieldId).type('abc').should('have.value', 'abc');
            submitForm();
            getErrors(fieldId).should('have.length', 1);
            cy.get('.form-error-test-form-url').contains('Please fill valid url');

            cy.get('#' + fieldId).clear().type('https://symfony.com').should('have.value', 'https://symfony.com');
            submitForm();
            getErrors(fieldId).should('have.length', 0);
        });

    });
});

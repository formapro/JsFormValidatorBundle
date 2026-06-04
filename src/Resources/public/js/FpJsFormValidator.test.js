import './FpJsFormValidator';

describe('FpJsFormValidator prototypes', () => {
    test('preparePrototype uses the prototype item name as the default id segment', () => {
        const prototype = {
            name: 'form[items][__name__]',
            id: 'form_items___name__',
            children: {
                title: {
                    name: 'form[items][__name__][title]',
                    id: 'form_items___name___title',
                    children: {},
                },
            },
        };

        const prepared = window.FpJsFormValidator.preparePrototype(prototype, '0');

        expect(prepared.name).toBe('form[items][0]');
        expect(prepared.id).toBe('form_items_0');
        expect(prepared.children.title.name).toBe('form[items][0][title]');
        expect(prepared.children.title.id).toBe('form_items_0_title');
    });

    test('addPrototype does not duplicate the parent id in generated child ids', () => {
        const parent = window.FpJsFormValidator.createElement({
            id: 'form_items',
            name: 'form[items]',
            type: '',
            invalidMessage: '',
            bubbling: false,
            disabled: false,
            transformers: [],
            data: {},
            children: {},
            prototype: {
                id: 'form_items___name__',
                name: 'form[items][__name__]',
                type: '',
                invalidMessage: '',
                bubbling: false,
                disabled: false,
                transformers: [],
                data: {},
                children: {},
            },
        });

        window.FpJsFormValidator.customizeMethods.addPrototype.apply([{ jsFormValidator: parent }], ['0']);

        expect(parent.children[0].id).toBe('form_items_0');
        expect(parent.children[0].name).toBe('form[items][0]');
        expect(parent.children[0].parent).toBe(parent);
    });
});

describe('FpJsFormValidator error mapping', () => {
    function createElement(id, name) {
        const element = new window.FpJsFormElement();
        element.id = id;
        element.name = name || id;
        element.domNode = document.createElement('input');
        element.showErrors = jest.fn();

        return element;
    }

    function addChild(parent, name, child) {
        parent.children[name] = child;
        child.parent = parent;

        return child;
    }

    function addConstraint(element, validate) {
        element.data.form = {
            constraints: [{
                groups: ['Default'],
                validate,
            }],
            getters: {},
            groups: ['Default'],
        };
    }

    test('keeps plain string errors on the validated element', () => {
        const element = createElement('user_email');
        addConstraint(element, () => ['Invalid email.']);

        expect(element.validate()).toBe(false);

        expect(element.errors['form-error-user-email']).toEqual(['Invalid email.']);
        expect(element.showErrors).toHaveBeenLastCalledWith(
            ['Invalid email.'],
            'form-error-user-email'
        );
    });

    test('routes structured errors to a direct child path', () => {
        const form = createElement('user');
        const email = addChild(form, 'email', createElement('user_email'));
        addConstraint(form, () => {
            const error = new window.FpJsFormError('Email is already used.');
            error.atPath = 'email';

            return [error];
        });

        expect(form.validate()).toBe(false);

        expect(form.errors['form-error-user']).toEqual([]);
        expect(email.errors['form-error-user']).toEqual(['Email is already used.']);
        expect(email.showErrors).toHaveBeenLastCalledWith(
            ['Email is already used.'],
            'form-error-user'
        );
    });

    test('routes structured errors to a nested child path', () => {
        const form = createElement('user');
        const address = addChild(form, 'address', createElement('user_address'));
        const street = addChild(address, 'street', createElement('user_address_street'));
        addConstraint(form, () => {
            const error = new window.FpJsFormError('Street is required.');
            error.atPath = 'address.street';

            return [error];
        });

        expect(form.validate()).toBe(false);

        expect(street.errors['form-error-user']).toEqual(['Street is required.']);
        expect(street.showErrors).toHaveBeenLastCalledWith(
            ['Street is required.'],
            'form-error-user'
        );
    });

    test('falls back to the validated element when a child path cannot be resolved', () => {
        const form = createElement('user');
        addConstraint(form, () => {
            const error = new window.FpJsFormError('Payment method is invalid.');
            error.atPath = 'payment';

            return [error];
        });

        expect(form.validate()).toBe(false);

        expect(form.errors['form-error-user']).toEqual(['Payment method is invalid.']);
        expect(form.showErrors).toHaveBeenLastCalledWith(
            ['Payment method is invalid.'],
            'form-error-user'
        );
    });

    test('clears previous routed errors before revalidating', () => {
        const form = createElement('user');
        const email = addChild(form, 'email', createElement('user_email'));
        let shouldFail = true;
        addConstraint(form, () => {
            if (!shouldFail) {
                return [];
            }

            const error = new window.FpJsFormError('Email is already used.');
            error.atPath = 'email';

            return [error];
        });

        expect(form.validate()).toBe(false);

        shouldFail = false;
        expect(form.validate()).toBe(true);

        expect(email.errors['form-error-user']).toEqual([]);
        expect(email.showErrors).toHaveBeenLastCalledWith([], 'form-error-user');
    });

    test('stores errors for model-only elements without requiring a DOM node', () => {
        const element = createElement('model_only');
        element.domNode = null;
        addConstraint(element, () => ['Model error.']);

        expect(element.validate()).toBe(false);

        expect(element.errors['form-error-model-only']).toEqual(['Model error.']);
        expect(element.showErrors).not.toHaveBeenCalled();
    });
});

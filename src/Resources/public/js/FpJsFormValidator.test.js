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

describe('FpJsFormValidator submit flow', () => {
    afterEach(() => {
        window.FpJsFormValidator.ajax.queue = 0;
        window.FpJsFormValidator.ajax.callbacks = [];
    });

    const createElement = (valid, form) => ({
        domNode: form || {},
        errors: {},
        children: {},
        validateRecursively: jest.fn(),
        onValidate: jest.fn(),
        isValid: jest.fn(() => valid),
        submitForm: jest.fn(),
    });

    const submit = (element, event) => {
        window.FpJsFormValidator.customizeMethods.submitForm.apply(
            [{ jsFormValidator: element }],
            [event],
        );
    };

    test('lets a valid native submit event continue so the original submitter is preserved', () => {
        const event = { preventDefault: jest.fn() };
        const element = createElement(true);

        submit(element, event);

        expect(element.validateRecursively).toHaveBeenCalled();
        expect(element.onValidate).toHaveBeenCalledWith({}, event);
        expect(event.preventDefault).not.toHaveBeenCalled();
        expect(element.submitForm).not.toHaveBeenCalled();
    });

    test('prevents a native submit event when validation fails', () => {
        const event = { preventDefault: jest.fn() };
        const element = createElement(false);

        submit(element, event);

        expect(element.onValidate).toHaveBeenCalledWith({}, event);
        expect(event.preventDefault).toHaveBeenCalled();
        expect(element.submitForm).not.toHaveBeenCalled();
    });

    test('re-submits an async valid native event with the original submitter', () => {
        const submitter = {};
        const form = { requestSubmit: jest.fn() };
        const event = { preventDefault: jest.fn(), submitter };
        const element = createElement(true, form);
        window.FpJsFormValidator.ajax.queue = 1;

        submit(element, event);

        expect(event.preventDefault).toHaveBeenCalledTimes(1);
        expect(window.FpJsFormValidator.ajax.callbacks).toHaveLength(1);
        expect(form.requestSubmit).not.toHaveBeenCalled();

        window.FpJsFormValidator.ajax.queue = 0;
        window.FpJsFormValidator.ajax.callbacks[0]();

        expect(form.__fpJsFormValidatorSubmitting).toBe(true);
        expect(form.requestSubmit).toHaveBeenCalledWith(submitter);
        expect(element.submitForm).not.toHaveBeenCalled();
    });

    test('allows a guarded re-submitted event to continue without validating again', () => {
        const form = { __fpJsFormValidatorSubmitting: true };
        const event = { preventDefault: jest.fn() };
        const element = createElement(true, form);

        submit(element, event);

        expect(form.__fpJsFormValidatorSubmitting).toBeUndefined();
        expect(element.validateRecursively).not.toHaveBeenCalled();
        expect(event.preventDefault).not.toHaveBeenCalled();
    });
});

describe('FpJsFormValidator runtime helpers', () => {
    afterEach(() => {
        document.body.innerHTML = '';
        window.FpJsFormValidator.forms = {};
        window.FpJsFormValidator.constraintsCounter = 0;
        window.FpJsFormValidator.ajax.queue = 0;
        window.FpJsFormValidator.ajax.callbacks = [];
        delete window.AppConstraint;
        delete global.$;
    });

    test('formats constraint messages and values', () => {
        expect(window.FpJsBaseConstraint.prepareMessage(
            'One item|{{ count }} items',
            { '{{ count }}': 3 },
            3,
        )).toBe('3 items');
        expect(window.FpJsBaseConstraint.prepareMessage(
            'One item|{{ count }} items',
            { '{{ count }}': 1 },
            1,
        )).toBe('One item');

        const date = new Date(2026, 5, 5, 9, 4, 3);
        date.format = jest.fn(() => '2026-06-05 09:04:03');
        expect(window.FpJsBaseConstraint.formatValue(date)).toBe('2026-06-05 09:04:03');
        expect(date.format).toHaveBeenCalledWith('Y-m-d H:i:s');
        expect(window.FpJsBaseConstraint.formatValue({})).toBe('object');
        expect(window.FpJsBaseConstraint.formatValue([])).toBe('array');
        expect(window.FpJsBaseConstraint.formatValue('name')).toBe('"name"');
        expect(window.FpJsBaseConstraint.formatValue(null)).toBe('null');
        expect(window.FpJsBaseConstraint.formatValue(true)).toBe('true');
        expect(window.FpJsBaseConstraint.formatValue(15)).toBe('15');
    });

    test('creates elements with DOM nodes, constraints, getters, and transformers', () => {
        document.body.innerHTML = '<input id="email" name="profile[email]" value="yes">';
        window.AppConstraint = function () {
            this.onCreate = function () {
                this.created = true;
            };
            this.validate = function () {
                return [];
            };
        };

        const element = window.FpJsFormValidator.createElement({
            id: 'email',
            name: 'profile[email]',
            type: '',
            invalidMessage: '',
            bubbling: false,
            disabled: false,
            transformers: [{
                name: 'Symfony\\Component\\Form\\Extension\\Core\\DataTransformer\\BooleanToStringTransformer',
                trueValue: 'yes',
            }],
            data: {
                form: {
                    groups: ['Default'],
                    constraints: {
                        'App\\Constraint': [{ message: 'Invalid.' }],
                    },
                    getters: {
                        customValue: {
                            'App\\Constraint': [{ groups: ['Default'] }],
                        },
                    },
                },
            },
            children: {},
        });

        expect(element.domNode).toBe(document.getElementById('email'));
        expect(element.domNode.jsFormValidator).toBe(element);
        expect(element.data.form.constraints).toHaveLength(1);
        expect(element.data.form.constraints[0].message).toBe('Invalid.');
        expect(element.data.form.constraints[0].created).toBe(true);
        expect(element.data.form.constraints[0].uniqueId).toBe(0);
        expect(element.data.form.getters.customValue).toHaveLength(1);
        expect(element.transformers).toHaveLength(1);
        expect(window.FpJsFormValidator.getElementValue(element)).toBe(true);
    });

    test('validates constraints, callback getters, and dynamic validation groups', () => {
        const parent = new window.FpJsFormElement();
        parent.id = 'profile';
        parent.groups = jest.fn(() => ['Custom']);

        const element = new window.FpJsFormElement();
        element.id = 'profile_name';
        element.parent = parent;
        element.domNode = document.createElement('input');
        element.domNode.value = 'value';
        element.callbacks.customValue = jest.fn(() => 'callback-value');

        const fieldConstraint = {
            groups: ['Custom'],
            validate: jest.fn(() => ['Field error.']),
        };
        const getterConstraint = {
            groups: ['Custom'],
            validate: jest.fn(() => ['Getter error.']),
        };
        element.data = {
            form: {
                groups: 'profile',
                constraints: [fieldConstraint],
                getters: {
                    customValue: [getterConstraint],
                },
            },
        };

        const errors = window.FpJsFormValidator.validateElement(element);

        expect(parent.groups).toHaveBeenCalled();
        expect(fieldConstraint.validate).toHaveBeenCalledWith('value', element);
        expect(getterConstraint.validate).toHaveBeenCalledWith('callback-value', element);
        expect(errors.map((error) => error.message)).toEqual(['Field error.', 'Getter error.']);
        expect(window.FpJsFormValidator.checkValidationGroups(['Other'], fieldConstraint)).toBe(false);
    });

    test('checks embedded validity rules and valid constraints', () => {
        const validConstraint = new window.SymfonyComponentValidatorConstraintsValid();
        const element = new window.FpJsFormElement();
        element.data.form = { constraints: [validConstraint] };

        expect(window.FpJsFormValidator.getElementValidConstraint(element)).toBe(validConstraint);
        expect(window.FpJsFormValidator.shouldValidEmbedded(element)).toBe(true);

        const collectionChild = new window.FpJsFormElement();
        collectionChild.parent = {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\CollectionType',
        };

        expect(window.FpJsFormValidator.shouldValidEmbedded(collectionChild)).toBe(true);
        expect(window.FpJsFormValidator.shouldValidEmbedded(new window.FpJsFormElement())).toBe(false);
    });

    test('extracts values from checkbox, select, collection, and mapped children', () => {
        const checkbox = new window.FpJsFormElement();
        checkbox.type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\CheckboxType';
        checkbox.domNode = { checked: true };

        const selectNode = document.createElement('select');
        selectNode.multiple = true;
        selectNode.innerHTML = '<option value="a" selected>A</option><option value="b">B</option><option value="c" selected>C</option>';
        const select = new window.FpJsFormElement();
        select.type = '';
        select.domNode = selectNode;

        const child = new window.FpJsFormElement();
        child.name = 'child';
        child.domNode = { value: 'child-value', tagName: 'input' };
        const collection = new window.FpJsFormElement();
        collection.type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\CollectionType';
        collection.children = { first: child };

        const mapped = new window.FpJsFormElement();
        mapped.children = { child };
        mapped.transformers = [{
            reverseTransform: jest.fn((value) => value.child),
        }];

        expect(window.FpJsFormValidator.getElementValue(checkbox)).toBe(true);
        expect(window.FpJsFormValidator.getElementValue(select)).toEqual(['c', 'a']);
        expect(window.FpJsFormValidator.getElementValue(collection)).toEqual({ first: 'child-value' });
        expect(window.FpJsFormValidator.getElementValue(mapped)).toBe('child-value');
    });

    test('finds DOM nodes and forms through ids, names, and descendants', () => {
        document.body.innerHTML = '<form id="profile"><div><input name="profile[email]" value="a@b.test"></div></form>';
        const named = window.FpJsFormValidator.findDomElement({
            id: 'missing',
            name: 'profile[email]',
        });
        const formElement = new window.FpJsFormElement();
        formElement.id = 'profile';
        formElement.domNode = document.getElementById('profile');
        const child = new window.FpJsFormElement();
        child.domNode = named;
        formElement.children.email = child;

        expect(named).toBe(document.getElementsByName('profile[email]')[0]);
        expect(window.FpJsFormValidator.findFormElement(formElement)).toBe(formElement.domNode);
        expect(window.FpJsFormValidator.findFormElement({ domNode: null, children: { email: child } })).toBe(formElement.domNode);
        expect(window.FpJsFormValidator.findParentForm(named)).toBe(formElement.domNode);
        expect(window.FpJsFormValidator.findParentForm(document.createTextNode('orphan'))).toBeNull();
        expect(window.FpJsFormValidator.findRealChildElement({ domNode: null, children: { email: child } })).toBe(named);
    });

    test('renders, clears, and bubbles errors through DOM helpers', () => {
        document.body.innerHTML = '<form id="profile"><input id="profile_email"></form>';
        const input = document.getElementById('profile_email');
        const element = new window.FpJsFormElement();
        element.id = 'profile_email';
        element.domNode = input;

        element.showErrors.apply(input, [['First error.', 'Second error.'], 'source-one']);
        expect(input.previousSibling.className).toBe('form-errors');
        expect(input.previousSibling.childNodes).toHaveLength(2);

        element.showErrors.apply(input, [['Replacement error.'], 'source-one']);
        expect(input.previousSibling.childNodes).toHaveLength(1);
        expect(input.previousSibling.textContent).toBe('Replacement error.');

        element.errors['source-one'] = ['Replacement error.'];
        element.clearErrors('source-one');
        expect(element.errors['source-one']).toEqual([]);

        const root = new window.FpJsFormElement();
        const child = new window.FpJsFormElement();
        child.parent = root;
        child.bubbling = true;
        root.children.child = child;

        expect(window.FpJsFormValidator.getErrorPathElement(child)).toBe(root);
        expect(window.FpJsFormValidator.getRootElement(child)).toBe(root);
        expect(window.FpJsFormValidator.findErrorDomNode(root)).toBeNull();
    });

    test('collects nested errors and utility lengths', () => {
        const root = new window.FpJsFormElement();
        root.id = 'root';
        root.errors = { rootSource: ['Root error.'] };
        const child = new window.FpJsFormElement();
        child.id = 'child';
        child.errors = { childSource: [] };
        root.children.child = child;

        expect(window.FpJsFormValidator.getAllErrors(root, null)).toEqual({
            root: { rootSource: ['Root error.'] },
        });
        expect(window.FpJsFormValidator.cloneObject({ nested: { value: 1 }, list: [1, 2] })).toEqual({
            nested: { value: 1 },
            list: [1, 2],
        });
        expect(window.FpJsFormValidator.isValueEmty(undefined)).toBe(true);
        expect(window.FpJsFormValidator.isValueEmty('')).toBe(true);
        expect(window.FpJsFormValidator.isValueEmty('x')).toBe(false);
        expect(window.FpJsFormValidator.isValueArray([])).toBe(true);
        expect(window.FpJsFormValidator.isValueObject({})).toBe(true);
        expect(window.FpJsFormValidator.getValueLength({ a: 1, b: 2 })).toBe(2);
        expect(window.FpJsFormValidator.getValueLength(12)).toBeUndefined();
    });

    test('customizes elements and reports unknown methods', () => {
        const domNode = document.createElement('input');
        const element = new window.FpJsFormElement();
        element.validate = jest.fn(() => true);
        element.validateRecursively = jest.fn(() => true);
        domNode.jsFormValidator = element;

        window.FpJsFormValidator.customize(domNode, {
            customEvents: function () {
                this.customEventsAttached = true;
            },
            onValidate: 'callback',
        });

        expect(domNode.customEventsAttached).toBe(true);
        expect(element.onValidate).toBe('callback');
        expect(window.FpJsFormValidator.customize(domNode)).toEqual([element]);
        expect(window.FpJsFormValidator.customize(domNode, 'validate', { recursive: true, findUniqueConstraint: false })).toBe(true);
        expect(element.validateRecursively).toHaveBeenCalled();

        global.$ = { error: jest.fn() };
        expect(window.FpJsFormValidator.customize(domNode, 'missingMethod')).toBe(window.FpJsFormValidator);
        expect(global.$.error).toHaveBeenCalledWith('Method missingMethod does not exist');
    });

    test('serializes and completes ajax requests', () => {
        const ajax = window.FpJsFormValidator.ajax;
        const request = {
            open: jest.fn(),
            setRequestHeader: jest.fn(),
            send: jest.fn(),
            readyState: 0,
            status: 0,
            responseText: '',
        };
        ajax.createRequest = jest.fn(() => request);
        const callback = jest.fn();
        const queueCallback = jest.fn();
        ajax.callbacks = [queueCallback];

        expect(ajax.serializeData({ profile: { email: 'a@b.test' }, page: 2 }, null)).toBe('profile%5Bemail%5D=a%40b.test&page=2');

        ajax.sendRequest('/check', { id: 15 }, callback);
        expect(request.open).toHaveBeenCalledWith('POST', '/check', true);
        expect(request.setRequestHeader).toHaveBeenCalledWith('Content-Type', 'application/x-www-form-urlencoded');
        expect(request.send).toHaveBeenCalledWith('id=15');
        expect(ajax.queue).toBe(1);

        request.readyState = 4;
        request.status = 200;
        request.responseText = 'true';
        request.onreadystatechange();

        expect(callback).toHaveBeenCalledWith('true');
        expect(ajax.queue).toBe(0);
        expect(queueCallback).toHaveBeenCalled();
    });
});

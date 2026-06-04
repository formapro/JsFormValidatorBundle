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

import '../FpJsFormValidator';
import FpJsFormValidatorBundleFormConstraintUniqueEntity from './UniqueEntity';

describe('FpJsFormValidatorBundleFormConstraintUniqueEntity', () => {
    afterEach(() => {
        window.FpJsFormValidator.config = {};
        jest.restoreAllMocks();
    });

    test('sends the current entity id with the uniqueness request', () => {
        const constraint = new FpJsFormValidatorBundleFormConstraintUniqueEntity();
        constraint.fields = ['email'];
        constraint.entityName = 'App\\Entity\\User';
        constraint.entityId = 15;
        constraint.uniqueId = 1;

        window.FpJsFormValidator.config = {
            routing: {
                check_unique_entity: '/check_unique_entity',
            },
        };

        const sendRequest = jest
            .spyOn(window.FpJsFormValidator.ajax, 'sendRequest')
            .mockImplementation(() => {});

        const element = {
            children: {
                email: {
                    name: 'email',
                    type: '',
                    transformers: [],
                    children: {},
                    domNode: {
                        tagName: 'input',
                        value: 'john@example.com',
                    },
                },
            },
        };

        expect(constraint.validate(null, element)).toStrictEqual([]);
        expect(sendRequest).toHaveBeenCalledWith(
            '/check_unique_entity',
            expect.objectContaining({
                entityName: 'App\\Entity\\User',
                entityId: 15,
                data: {
                    email: 'john@example.com',
                },
            }),
            expect.any(Function),
        );
    });
});

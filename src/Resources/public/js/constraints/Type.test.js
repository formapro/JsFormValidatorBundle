import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsType from './Type';

const constraintsType = new SymfonyComponentValidatorConstraintsType();
constraintsType.message = '{{ value }} is not typ of {{ type }}';

test.each([
    ['array', [], []],
    ['bool', true, []],
    ['boolean', false, []],
    ['callable', () => {}, []],
    ['float', 1.1, []],
    ['double', 1.1, []],
    ['real', 1.1, []],
    ['int', 1, []],
    ['integer', 1, []],
    ['long', 1, []],
    ['null', null, []],
    ['numeric', 1, []],
    ['object', {}, []],
    ['scalar', true, []],
    ['scalar', 1, []],
    ['scalar', 'a', []],
    ['', 'a', []],
    ['string', 'a', []],
])(
    'SymfonyComponentValidatorConstraintsType',
    (type, value, expected) => {
        constraintsType.type = type;
        expect(constraintsType.validate(value)).toStrictEqual(expected);
    },
);

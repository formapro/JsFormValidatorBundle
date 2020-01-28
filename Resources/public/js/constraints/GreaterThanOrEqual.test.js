import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsGreaterThanOrEqual from './GreaterThanOrEqual';

const constraintsGraterThanOrEqual = new SymfonyComponentValidatorConstraintsGreaterThanOrEqual();
constraintsGraterThanOrEqual.message = '{{ value }} is not greater than or equal to {{ compared_value }}';

test.each([
    [null, null, []],
    [1, 2, []],
    ['', '', []],
    ['a', 'b', []],
    [1, 1, []],
    [2, 1, ['1 is not greater than or equal to 2']],
    ['a', 'a', []],
    ['b', 'a', ["\"a\" is not greater than or equal to \"b\""]],
])(
    'SymfonyComponentValidatorConstraintsGreaterThanOrEqual',
    (valueToSet, value, expected) => {
        constraintsGraterThanOrEqual.value = valueToSet;
        expect(constraintsGraterThanOrEqual.validate(value)).toStrictEqual(expected);
    },
);

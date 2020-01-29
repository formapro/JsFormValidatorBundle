import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsLessThanOrEqual from './LessThanOrEqual';

const constraintsLessThanOrEqual = new SymfonyComponentValidatorConstraintsLessThanOrEqual();
constraintsLessThanOrEqual.message = '{{ value }} is not less than or equal to {{ compared_value }}';

test.each([
    [null, null, []],
    [2, 1, []],
    ['', '', []],
    ['b', 'a', []],
    [1, 1, []],
    [1, 2, ['2 is not less than or equal to 1']],
    ['a', 'a', []],
    ['a', 'b', ["\"b\" is not less than or equal to \"a\""]],
])(
    'SymfonyComponentValidatorConstraintsLessThanOrEqual',
    (valueToSet, value, expected) => {
        constraintsLessThanOrEqual.value = valueToSet;
        expect(constraintsLessThanOrEqual.validate(value)).toStrictEqual(expected);
    },
);

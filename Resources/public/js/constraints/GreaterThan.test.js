import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsGreaterThan from './GreaterThan';

const constraintsGraterThan = new SymfonyComponentValidatorConstraintsGreaterThan();
constraintsGraterThan.message = '{{ value }} is not greater than {{ compared_value }}';

test.each([
    [null, null, []],
    [1, 2, []],
    ['', '', []],
    ['a', 'b', []],
    [1, 1, ['1 is not greater than 1']],
    [2, 1, ['1 is not greater than 2']],
    ['b', 'a', ["\"a\" is not greater than \"b\""]],
])(
    'SymfonyComponentValidatorConstraintsGreaterThan',
    (valueToSet, value, expected) => {
        constraintsGraterThan.value = valueToSet;
        expect(constraintsGraterThan.validate(value)).toStrictEqual(expected);
    },
);

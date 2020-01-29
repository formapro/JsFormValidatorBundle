import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsLessThan from './LessThan';

const constraintsLessThan = new SymfonyComponentValidatorConstraintsLessThan();
constraintsLessThan.message = '{{ value }} is not less than {{ compared_value }}';

test.each([
    [null, null, []],
    [2, 1, []],
    ['', '', []],
    ['b', 'a', []],
    [1, 1, ['1 is not less than 1']],
    [1, 2, ['2 is not less than 1']],
    ['a', 'b', ["\"b\" is not less than \"a\""]],
])(
    'SymfonyComponentValidatorConstraintsLessThan',
    (valueToSet, value, expected) => {
        constraintsLessThan.value = valueToSet;
        expect(constraintsLessThan.validate(value)).toStrictEqual(expected);
    },
);

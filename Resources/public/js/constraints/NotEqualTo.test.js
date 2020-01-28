import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsNotEqualTo from './NotEqualTo';

const constraintsNotEqualTo = new SymfonyComponentValidatorConstraintsNotEqualTo();
constraintsNotEqualTo.message = '{{ value }} is equal to {{ compared_value }} ({{ compared_value_type }})';

test.each([
    [null, null, ['null is equal to null (null)']],
    [[], [], []], // !!!
    ['', '', []], // !!!
    [1, 1, ['1 is equal to 1 (1)']],
    ['a', 'a', ['\"a\" is equal to \"a\" (\"a\")']],
    ['a', 'b', []],
    [1, 2, []],
    [{ a: 'a' }, { a: 'a' }, []],
])(
    'SymfonyComponentValidatorConstraintsNotEqualTo',
    (valueToSet, value, expected) => {
        constraintsNotEqualTo.value = valueToSet;
        expect(constraintsNotEqualTo.validate(value)).toStrictEqual(expected);
    },
);

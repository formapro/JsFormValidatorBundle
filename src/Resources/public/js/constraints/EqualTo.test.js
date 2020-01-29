import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsEqualTo from './EqualTo';

const constraintsEqualTo = new SymfonyComponentValidatorConstraintsEqualTo();
constraintsEqualTo.message = '{{ value }} is not equal to {{ compared_value }} ({{ compared_value_type }})';

test.each([
    [null, null, []],
    [[], [], []],
    ['', '', []],
    [1, 1, []],
    ['a', 'a', []],
    ['a', 'b', ['\"b\" is not equal to \"a\" (\"a\")']],
    [1, 2, ['2 is not equal to 1 (1)']],
    [{ a: 'a' }, { a: 'a' }, ['object is not equal to object (object)']],
])(
    'SymfonyComponentValidatorConstraintsEqualTo',
    (valueToSet, value, expected) => {
        constraintsEqualTo.value = valueToSet;
        expect(constraintsEqualTo.validate(value)).toStrictEqual(expected);
    },
);

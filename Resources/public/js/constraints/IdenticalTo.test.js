import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsIdenticalTo from './IdenticalTo';

const constraintsIdenticalTo = new SymfonyComponentValidatorConstraintsIdenticalTo();
constraintsIdenticalTo.message = '{{ value }} is not identical to {{ compared_value }} ({{ compared_value_type }})';

test.each([
    [null, null, []],
    ['', '', []],
    [1, 1, []],
    ['a', 'a', []],
    ['a', 'b', ['\"b\" is not identical to \"a\" (\"a\")']],
    [1, 2, ['2 is not identical to 1 (1)']],
    [{ a: 'a' }, { a: 'a' }, ['object is not identical to object (object)']],
    [[1], [1], ['array is not identical to array (array)']], // !!!
])(
    'SymfonyComponentValidatorConstraintsIdenticalTo',
    (valueToSet, value, expected) => {
        constraintsIdenticalTo.value = valueToSet;
        expect(constraintsIdenticalTo.validate(value)).toStrictEqual(expected);
    },
);


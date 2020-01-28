import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsNotIdenticalTo from './NotIdenticalTo';

const constraintsNotIdenticalTo = new SymfonyComponentValidatorConstraintsNotIdenticalTo();
constraintsNotIdenticalTo.message = '{{ value }} is identical to {{ compared_value }} ({{ compared_value_type }})';

test.each([
    ['', '', []], // !!!
    ['a', 'b', []],
    [1, 2, []],
    [{ a: 'a' }, { a: 'a' }, []],
    [[1], [1], []], // !!!
    [null, null, ['null is identical to null (null)']],
    [1, 1, ['1 is identical to 1 (1)']],
    ['a', 'a', ['\"a\" is identical to \"a\" (\"a\")']],
])(
    'SymfonyComponentValidatorConstraintsNotIdenticalTo',
    (valueToSet, value, expected) => {
        constraintsNotIdenticalTo.value = valueToSet;
        expect(constraintsNotIdenticalTo.validate(value)).toStrictEqual(expected);
    },
);


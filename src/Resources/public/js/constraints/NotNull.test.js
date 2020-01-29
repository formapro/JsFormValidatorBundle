import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsNotNull from './NotNull';

const constraintsNotNull = new SymfonyComponentValidatorConstraintsNotNull();
constraintsNotNull.message = '{{ value }} is not null';

test.each([
    [null, ['null is not null']],
    [0, []],
    ['', []],
])(
    'SymfonyComponentValidatorConstraintsNotNull',
    (value, expected) => {
        expect(constraintsNotNull.validate(value)).toStrictEqual(expected);
    },
);

import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsIsNull from './IsNull';

const constraintsIsNull = new SymfonyComponentValidatorConstraintsIsNull();
constraintsIsNull.message = '{{ value }} is not null';

test.each([
    [null, []],
    [0, ['0 is not null']],
    ['', ['\"\" is not null']],
])(
    'SymfonyComponentValidatorConstraintsIsNull',
    (ipToTest, expected) => {
        expect(constraintsIsNull.validate(ipToTest)).toStrictEqual(expected);
    },
);

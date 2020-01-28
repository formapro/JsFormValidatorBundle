import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsEmail from './Email';

const constraintsEmail = new SymfonyComponentValidatorConstraintsEmail();
constraintsEmail.message = '{{ value }} is not valid email';

test.each([
    ['1@2.org', []],
    ['\\@shoh.co', ['\"\\@shoh.co\" is not valid email']],
    ['@shoh.co', ['\"@shoh.co\" is not valid email']],
    ['1@', ['\"1@\" is not valid email']],
])(
    'SymfonyComponentValidatorConstraintsEmail',
    (value, expected) => {
        expect(constraintsEmail.validate(value)).toStrictEqual(expected);
    },
);

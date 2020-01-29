import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsDateTime from './DateTime';

const constraintsDateTime = new SymfonyComponentValidatorConstraintsDateTime();
constraintsDateTime.message = '{{ value }} is not valid date time';

test.each([
    ['', []],
    ['2020-01-01 00:00:00', []],
    ['2020-01-01', ['\"2020-01-01\" is not valid date time']],
    ['00:00:00', ['\"00:00:00\" is not valid date time']],
])(
    'SymfonyComponentValidatorConstraintsDateTime',
    (value, expected) => {
        expect(constraintsDateTime.validate(value)).toStrictEqual(expected);
    },
);

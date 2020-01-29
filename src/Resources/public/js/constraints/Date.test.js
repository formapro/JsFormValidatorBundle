import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsDate from './Date';

const constraintsDate = new SymfonyComponentValidatorConstraintsDate();
constraintsDate.message = '{{ value }} is not valid date';

test.each([
    ['', []],
    ['2020-01-01', []],
    ['1', ['\"1\" is not valid date']],
    ['1.1.2020', ['\"1.1.2020\" is not valid date']],
    ['2020-1-1', ['\"2020-1-1\" is not valid date']],
])(
    'SymfonyComponentValidatorConstraintsDate',
    (value, expected) => {
        expect(constraintsDate.validate(value)).toStrictEqual(expected);
    },
);

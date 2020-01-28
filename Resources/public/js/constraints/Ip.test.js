import '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsIp from './Ip';

const constraintsIp = new SymfonyComponentValidatorConstraintsIp();
constraintsIp.message = '{{ value }} is not valid ip';

test.each([
    [null, []],
    ['', []],
    ['1', ['\"1\" is not valid ip']],
    ['1.1', ['\"1.1\" is not valid ip']],
    ['1.1.1', ['\"1.1.1\" is not valid ip']],
    ['localhost', ['\"localhost\" is not valid ip']],
    ['1.1.1.1', []],
    ['100.100.100.100', []],
    ['256.256.256.256', ['\"256.256.256.256\" is not valid ip']],
    ['::1', ['\"::1\" is not valid ip']], // !!!
])(
    'SymfonyComponentValidatorConstraintsIp',
    (ipToTest, expected) => {
        expect(constraintsIp.validate(ipToTest)).toStrictEqual(expected);
    },
);

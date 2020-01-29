import { FpJsFormElement } from '../FpJsFormValidator';
import SymfonyComponentValidatorConstraintsUrl from './Url';

const constraintsUrl = new SymfonyComponentValidatorConstraintsUrl();
constraintsUrl.message = '{{ value }} is not valid url';

const element = new FpJsFormElement();
element.domNode = {};

test.each([
    ['http://www.google.com', []],
    ['http://stackoverflow.com/questions/', []],
    ['http://stackoverflow.com/questions/', []],
    ['http://google.cz/search?hl=en&sxsrf=ACY', []],
    ['a', ['\"http://a\" is not valid url']], // !!!
    ['http://a', ['\"http://http://a\" is not valid url']], // !!!
])(
    'SymfonyComponentValidatorConstraintsUrl',
    (value, expected) => {
        expect(constraintsUrl.validate(value, element)).toStrictEqual(expected);
    },
);

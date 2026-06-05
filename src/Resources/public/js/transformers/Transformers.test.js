import ArrayToParts from './ArrayToParts';
import BooleanToString from './BooleanToString';
import ChoicesToBooleanArray from './ChoicesToBooleanArray';
import ChoicesToValues from './ChoicesToValues';
import ChoiceToBooleanArray from './ChoiceToBooleanArray';
import ChoiceToValue from './ChoiceToValue';
import DataTransformerChain from './DataTransformerChain';
import DateTimeToArray from './DateTimeToArray';
import ValueToDuplicates from './ValueToDuplicates';

describe('Symfony form data transformers', () => {
    test('ArrayToParts maps configured nested parts back to original keys', () => {
        const transformer = new ArrayToParts();
        transformer.partMapping = {
            date: ['year', 'month', 'day'],
            time: ['hour'],
        };

        expect(transformer.reverseTransform({
            date: { year: '2026', day: '05' },
            time: { hour: '09' },
        })).toEqual({
            year: '2026',
            day: '05',
            hour: '09',
        });
        expect(() => transformer.reverseTransform('2026-06-05')).toThrow('Expected an object.');
    });

    test('BooleanToString accepts booleans, configured true values, and empty false values', () => {
        const transformer = new BooleanToString();
        transformer.trueValue = 'yes';

        expect(transformer.reverseTransform(true)).toBe(true);
        expect(transformer.reverseTransform(false)).toBe(false);
        expect(transformer.reverseTransform('yes')).toBe(true);
        expect(transformer.reverseTransform('')).toBe(false);
        expect(transformer.reverseTransform(null)).toBe(false);
        expect(() => transformer.reverseTransform('no')).toThrow('Wrong type of value');
    });

    test('ChoicesToBooleanArray returns selected choices and rejects unknown keys', () => {
        const transformer = new ChoicesToBooleanArray();
        transformer.choiceList = {
            0: 'red',
            1: 'blue',
            2: 'green',
        };

        expect(transformer.reverseTransform({ 0: true, 1: false, 2: true })).toEqual(['red', 'green']);
        expect(() => transformer.reverseTransform('red')).toThrow('Unexpected value type');
        expect(() => transformer.reverseTransform({ 3: true })).toThrow('The choices "3" were not found.');
    });

    test('ChoiceToBooleanArray returns one choice, placeholders, or null', () => {
        const transformer = new ChoiceToBooleanArray();
        transformer.choiceList = {
            0: 'red',
            1: '',
        };

        expect(transformer.reverseTransform({ 0: true })).toBe('red');
        expect(transformer.reverseTransform({ 1: true })).toBeNull();
        expect(transformer.reverseTransform({ 0: false, 1: false })).toBeNull();
        expect(() => transformer.reverseTransform('red')).toThrow('Unexpected value type');
        expect(() => transformer.reverseTransform({ 3: true })).toThrow('The choice "3" does not exist');

        transformer.placeholderPresent = true;
        expect(transformer.reverseTransform({ placeholder: true })).toBeNull();
    });

    test('Choice value transformers remove empty submitted values', () => {
        const choices = new ChoicesToValues();
        const choice = new ChoiceToValue();

        expect(choices.reverseTransform(['', 'first', '', 'second'])).toEqual(['first', 'second']);
        expect(choice.reverseTransform(['', 'single'])).toEqual(['single']);
    });

    test('DataTransformerChain applies transformers in order', () => {
        const first = { reverseTransform: jest.fn((value) => value + '-first') };
        const second = { reverseTransform: jest.fn((value) => value + '-second') };
        const chain = new DataTransformerChain([first, second]);
        const element = {};

        expect(chain.reverseTransform('value', element)).toBe('value-first-second');
        expect(first.reverseTransform).toHaveBeenCalledWith('value', element);
        expect(second.reverseTransform).toHaveBeenCalledWith('value-first', element);
    });

    test('DateTimeToArray formats date and time parts with defaults', () => {
        const transformer = new DateTimeToArray();

        expect(transformer.reverseTransform({ year: '2026', month: '6', day: '5' })).toBe('2026-06-05');
        expect(transformer.reverseTransform({ hour: '9', minute: '4' })).toBe('09:04:00');
        expect(transformer.reverseTransform({ month: '12', second: '7' })).toBe('1970-12-01 00:00:07');
        expect(transformer.twoDigits('8')).toBe('08');
        expect(transformer.formatDate('{2}/{1}/{0}', ['2026', '06', '05'])).toBe('05/06/2026');
    });

    test('ValueToDuplicates reports repeated field mismatch on the first child', () => {
        const transformer = new ValueToDuplicates();
        transformer.keys = ['first'];
        const childDomNode = document.createElement('input');
        const element = {
            invalidMessage: 'Values must match.',
            children: {
                first: {
                    id: 'first',
                    domNode: childDomNode,
                },
            },
        };
        const customize = jest.fn();
        global.FpJsFormValidator = { customize };
        window.FpJsFormValidator = global.FpJsFormValidator;

        expect(transformer.reverseTransform({ first: 'secret', second: 'secret' }, element)).toBe('secret');
        expect(customize).toHaveBeenLastCalledWith(childDomNode, 'showErrors', {
            errors: [],
            sourceId: 'value-to-duplicates-first',
        });

        expect(transformer.reverseTransform({ first: 'secret', second: 'different' }, element)).toBe('secret');
        expect(customize).toHaveBeenLastCalledWith(childDomNode, 'showErrors', {
            errors: ['Values must match.'],
            sourceId: 'value-to-duplicates-first',
        });
    });
});

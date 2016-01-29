describe('SymfonyComponentValidatorConstraintsDate', function() {
    var constraint;

    before(function() {
        constraint = new SymfonyComponentValidatorConstraintsDate();
        constraint.message = '{{ value }} is not a valid date';
    });

    context('an invalid date format', function() {
        it('returns an array of errors', function() {
            var errors = constraint.validate('not-a-date');

            expect(errors).to.deep.equal(['not-a-date is not a valid date']);
        });
    });

    context('an empty value', function() {
        it('returns an empty array', function() {
            var errors = constraint.validate('');

            expect(errors).to.be.empty;
        });
    });

    context('a valid date', function() {
        it('returns an empty array', function() {
            var errors = constraint.validate('2002-03-04');

            expect(errors).to.be.empty;
        });
    });
});

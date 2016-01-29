describe('SymfonyComponentFormExtensionCoreDataTransformerDateTimeToArrayTransformer', function() {
    var transformer;

    before(function() {
        transformer = new SymfonyComponentFormExtensionCoreDataTransformerDateTimeToArrayTransformer();
    });

    describe('#reverseTransform', function() {
        context('a date', function() {
            it('returns a date string', function() {
                var result = transformer.reverseTransform({ year: ["2012"], month: ["2"], day: ["4"] });

                expect(result).to.equal('2012-02-04');
            });
        });

        context('an empty date', function() {
            it('returns empty string', function() {
                var result = transformer.reverseTransform({ year: [""], month: [""], day: [""] });

                expect(result).to.equal('');
            });
        });

        context('a datetime', function() {
            it('returns a datetime string', function() {
                var result = transformer.reverseTransform({ year: ["2002"], month: ["12"], day: ["3"], hour: ["4"], minute: ["43"], second: ["59"] });

                expect(result).to.equal('2002-12-03 04:43:59');
            });
        });
    });
});

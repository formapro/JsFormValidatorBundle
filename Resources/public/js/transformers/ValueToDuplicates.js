//noinspection JSUnusedGlobalSymbols
function SymfonyComponentFormExtensionCoreDataTransformerValueToDuplicatesTransformer() {
    this.keys = [];

    /**
     *
     * @param {{}} value
     * @param {FpJsFormElement} element
     */
    this.reverseTransform = function(value, element) {
        var initialValue = undefined;
        var errors = [];
        for (var key in value) {
            if (undefined === initialValue) {
                initialValue = value[key];
            }

            var child = element.children[this.keys[0]];
            if (value[key] !== initialValue) {
                errors.push(element.invalidMessage);
                break;
            }
        }
        FpJsFormValidator.customize(child.domNode, 'showErrors', {
            errors: errors,
            sourceId: 'value-to-duplicates-' + child.id
        });

        return initialValue;
    }
}
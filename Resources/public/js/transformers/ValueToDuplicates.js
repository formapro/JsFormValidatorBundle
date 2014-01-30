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
        for (var key in value) {
            if (undefined === initialValue) {
                initialValue = value[key];
            }

            if (value[key] !== initialValue) {
                var child = element.children[this.keys[0]];
                child.showErrors.apply(child.domNode, [[element.invalidMessage], 'value-to-duplicates']);
                break;
            }
        }

        return initialValue;
    }
}
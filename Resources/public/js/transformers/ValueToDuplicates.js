//noinspection JSUnusedGlobalSymbols
function SymfonyComponentFormExtensionCoreDataTransformerValueToDuplicatesTransformer() {
    this.keys = [];

    /**
     *
     * @param {{}} value
     * @param {FpJsFormElement} model
     */
    this.reverseTransform = function(value, model) {
        var initialValue = undefined;
        for (var key in value) {
            if (undefined === initialValue) {
                initialValue = value[key];
            }

            if (value[key] !== initialValue) {
                var message = model.getInvalidMessage();
                model.getChild(this.keys[0]).addErrors(message);
                break;
            }
        }

        return initialValue;
    }
}
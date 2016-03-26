//noinspection JSUnusedGlobalSymbols
function SymfonyComponentValidatorConstraintsCallback () {
    this.callback = null;
    this.methods  = [];

    /**
     * @param {*} value
     * @param {FpJsFormElement} element
     */
    this.validate = function(value, element) {
        if (!this.callback) {
            this.callback = [];
        }
        if (!this.methods.length) {
            this.methods = [this.callback];
        }

        for (var i in this.methods) {
            var method = FpJsFormValidator.getRealCallback(element, this.methods[i]);
            if (null !== method) {
                method.apply(element.domNode);
            } else {
                throw new Error('Can not find a "' + this.callback + '" callback for the element id="' + element.id + '" to validate the Callback constraint.');
            }
        }

        return [];
    }
}
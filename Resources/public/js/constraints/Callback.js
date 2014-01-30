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
        if (typeof this.callback == "string") {
            this.callback = [this.callback];
        }
        if (!this.methods) {
            this.methods = [this.callback];
        }

        for (var pairId in this.methods) {
            var className  = (1 == this.methods[pairId].length)
                ? null
                : this.methods[pairId][0];

            var methodName = (1 == this.methods[pairId].length)
                ? this.methods[pairId][0]
                : this.methods[pairId][1];

            var method = function(){};

            if (!element.callbacks[className] && typeof element.callbacks[methodName] == "function") {
                method = element.callbacks[methodName];
            } else if (element.callbacks[className] && typeof element.callbacks[className][methodName] == "function") {
                method = element.callbacks[className][methodName];
            } else if (typeof element.callbacks[methodName] == "function") {
                method = element.callbacks[methodName];
            }
        }

        method.apply(element.domNode);

        return [];
    }
}
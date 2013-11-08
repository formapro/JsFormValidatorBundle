/**
 * Validator model for the inputs
 *
 * @param options an object with extra options
 */
function FpJsFormType(options){
    this.constructor(options);
}

/**
 * Create an object
 * @constructor
 * @param options
 */
FpJsFormType.prototype.constructor = function(options) {
    this.elementsCache = {};

    // Process options
    for (var optionName in options) {
        switch (optionName) {
            case 'constraints':
                this[optionName] = this.parseConstraints(options[optionName]);
                break;
            case 'transformers':
                this[optionName] = this.parseTransformers(options[optionName]);
                break;
            default:
                this[optionName] = options[optionName];
                break;
        }
    }
    // Attach events
    this.bindEvents();
};

/**
 * Attach events
 */
FpJsFormType.prototype.bindEvents = function() {
    var self = this;

    for (var elementId in this['events']) {
        var len = this['events'][elementId].length;
        for (var i = 0; i < len; i++) {
            this.getFormElement(elementId).addEventListener(this['events'][elementId][i], function(event){
                if (false === self.validate()) {
                    event.preventDefault();
                }
            });
        }
    }
};

/**
 * Find (or get from cache) the form element by id
 * @param elementId
 */
FpJsFormType.prototype.getFormElement = function(elementId) {
    if (!this.elementsCache[elementId]) {
        this.elementsCache[elementId] = document.getElementById(elementId);

        // If this is the parent form - looking for real form tag for this id
        if (elementId === this['parentFormId']){

            // Create a function that gets the real form tag element
            var getParentFormElement = function(element) {
                if (element && 'form' === element.tagName.toLowerCase()) {
                    return element;
                } else if (element.parentNode instanceof HTMLElement) {
                    return getParentFormElement(element.parentNode);
                } else {
                    return undefined;
                }
            };
            // Get the real form tag
            this.elementsCache[elementId] = getParentFormElement(this.elementsCache[elementId]);
        }
    }

    return this.elementsCache[elementId];
};

/**
 * Get general value from the element and parse with transformers if they are exist
 * @returns {undefined}
 */
FpJsFormType.prototype.getElementValue = function()
{
    var value = undefined;
    var len   = this['transformers'].length;
    for (var i = 0; i < len; i++) {
        if (typeof this['transformers'][i]['reverseTransform'] === 'function') {
            value = this['transformers'][i]['reverseTransform'](value);
        }
    }

    var element = this.getFormElement(this.id);
    if (0 === len && element && undefined !== element.value) {
        value = element.value;
    }

    return value;
};

/**
 * Validate an input
 *
 * @returns {*}
 */
FpJsFormType.prototype.validate = function() {
    var value = this.getElementValue();
    var constr; // Constraint object
    var errors = []; // errors' container
    var i, len; // loop variables

    len = this['constraints'].length;
    for (i = 0; i < len; i++) {
        constr = this.constraints[i];
        constr.errors = [];
        constr.validate(value);
        errors = errors.concat(constr.errors);
    }
    this.showErrors(errors);

    return 0 === errors.length;
};

/**
 * Fill out error-list with the received errors.
 *
 * @param {Array} errors
 */
FpJsFormType.prototype.showErrors = function(errors) {
    console.log(errors);
};

/**
 * Convert constraints mapping to the real context objects
 *
 * @param {Object} mapping
 * @returns {Array}
 */
FpJsFormType.prototype.parseConstraints = function(mapping) {
    var constraints = [];

    for (var constrName in mapping) {
        if (undefined !== window[constrName]) {
            for (var i = 0; i < mapping[constrName].length; i++) {
                var constr = new window[constrName]();
                for (var param in mapping[constrName][i]) {
                    constr[param] = mapping[constrName][i][param];
                }

                if (typeof constr['onCreate'] === 'function') {
                    constr['onCreate']();
                }
                constraints.push(constr);
            }
        }
    }

    return constraints;
};

/**
 * Convert transformers mapping to the real objects
 *
 * @param {Object} mapping
 * @returns {Array}
 */
FpJsFormType.prototype.parseTransformers = function(mapping) {
    var transformers = [];

    var len = mapping.length;
    for (var i = 0; i < len; i++) {
        var transName = mapping[i]['name'];
        if (undefined !== window[transName]) {
            transformers.push(new window[transName](this, mapping[i]));
        }
    }

    return transformers;
};
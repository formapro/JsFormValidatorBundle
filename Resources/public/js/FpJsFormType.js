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
        var len = this['events'][elementId];
        for (var i = 0; i < len; i++) {
            document.getElementById(elementId).addEventListener(this['events'][elementId][i], function(event){
                if (false === self.validate()) {
                    event.preventDefault();
                }
            });
        }
    }
};

/**
 * Get general value from the element and parse with transformers if they are exist
 * @returns {undefined}
 */
FpJsFormType.prototype.getElementValue = function()
{
    var value = undefined;
    var element = document.getElementById(this.id);
    var len = this['transformers'].length;
    if (0 === len) {
        value = element.value;
    } else {
        for (var i = 0; i < len; i++) {
            if (typeof this['transformers'][i]['reverseTransform'] === 'function') {
                value = this['transformers'][i]['reverseTransform'](value);
            }

        }
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
FpJsFieldValidator.prototype.showErrors = function(errors) {
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
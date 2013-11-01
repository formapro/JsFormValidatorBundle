/**
 * Validator model for the inputs
 * Extends from FpJsValidatorModel
 *
 * @param id element id attribute
 * @param constraints
 * @param options an object with extra options
 */
function FpJsFieldValidator(id, constraints, options){
    this.constructor(id, constraints, options);
}

/**
 * Input validator's config
 * @type {{}}
 */
FpJsFieldValidator.prototype.config = {
    errorTagClass:  'error',
    errorListClass: 'error-list',
    events:         ['change']
};

/**
 * FpJsFieldValidator constructor
 * @param id
 * @param constraints
 * @param options
 *
 * @constructor
 */
FpJsFieldValidator.prototype.constructor = function(id, constraints, options) {
    // Merge default config with the received options
    for (var name in options) {
        this.config[name] = options[name];
    }

    // Convert constraints from simplified objects to the Constraint models
    this.constraints = this.parseConstraints(constraints);

    // Set a relation with the DOM element
    this.element = document.getElementById(id);
    if (this.element) {
        // Add self to the DOM element
        this.element['fpValidator'] = this;
        // Bind the validate event for each event type
        for (var i = 0; i < this.config.events.length; i++) {
            this.element.addEventListener(this.config.events[i], function(){
                return this['fpValidator'].validate();
            });
        }
    }
};

/**
 * Validate an input
 *
 * @returns {*}
 */
FpJsFieldValidator.prototype.validate = function() {
    // Check if functional is enabled and has constraints
    if (false === this.config['isEnabled']|| 0 == this.constraints.length) return true;

    var errors = [];
    for (var i = 0; i < this.constraints.length; i++) {
        var constr = this.constraints[i];
        constr.errors = [];
        constr.validate(this.element.value);
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
    var listElement = this.getErrorListElement();
    var currClass  = this.element.className;
    var errorClass = this.config.errorTagClass;

    if (errors.length) {
        // Add li-errors to the list
        for (var i in errors) {
            var li = document.createElement('li');
            li.innerHTML = errors[i];
            listElement.appendChild(li);
        }

        // Add error class to the form tag if it does not exist
        var regexClass = new RegExp('(\\s|^)' + errorClass + '(\\s|$)');
        if (!regexClass.test(currClass)) {
            this.element.className = String(currClass + ' ' + errorClass).trim();
        }
    } else {
        // Remove list at all
        if (undefined !== listElement) {
            listElement.parentNode.removeChild(listElement);
        }
        // Remove error class from the form tag
        this.element.className = currClass.replace(errorClass, '');
    }
};

/**
 * Get the error list container (DOM element)
 * @returns {HTMLElement}
 */
FpJsFieldValidator.prototype.getErrorListElement = function() {
    var ul = null;
    // Search for an error-list
    var parent = this.element.parentNode;
    var children = Array.prototype.slice.call(parent.children);
    for (var numChild in children) {
        var tag      = children[numChild].tagName.toLowerCase();
        var hasClass = children[numChild].className.search(this.config.errorListClass) !== -1;
        if (tag === 'ul' && true === hasClass) {
            ul = children[numChild];
            break;
        }
    }

    // Create a new if not found
    if (!ul) {
        ul = document.createElement("ul");
        ul.className = this.config.errorListClass;
        parent.insertBefore(ul, parent.firstChild);
    }

    return ul;
};

/**
 * Convert constraints mapping to the real context objects
 *
 * @param {Object} mapping
 * @returns {Array}
 */
FpJsFieldValidator.prototype.parseConstraints = function(mapping) {
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
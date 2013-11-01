/**
 * Validator model for the form
 * Extends from FpJsValidatorModel
 *
 * @param id form id attribute
 * @param getters
 * @param options an object with extra options
 */
function FpJsFormValidator(id, getters, options){
    this.constructor(id, getters, options);
}

/**
 * Form validator config
 * @type {{}}
 */
FpJsFormValidator.prototype.config = {
    errorTagClass:  'error',
    errorListClass: 'error-list',
    events:         ['submit']
};

/**
 * FpJsFormValidator constructor
 * @param id
 * @param getters
 * @param options
 *
 * @constructor
 */
FpJsFormValidator.prototype.constructor = function(id, getters, options) {
    this.id = id;

    // Merge default config with the received options
    for (var name in options) {
        this.config[name] = options[name];
    }

    /**
     * An extra method to find closest form element
     */
    this.findClosestForm = function(element) {
        var parent = element.parentNode;
        if (parent && parent.tagName.toLowerCase() == 'form') {
            return parent;
        } else if (parent instanceof HTMLElement) {
            return this.findClosestForm(element);
        } else {
            return undefined;
        }
    };

    // Set a relation with the DOM element
    this.element = document.getElementById(this.id);
    // By default Symfony sets form id to inner div, but not to form element.
    // For this reason, if current element is not form, we need to try to find it in parents
    if (this.element && 'form' !== this.element.tagName.toLowerCase()) {
        this.element = this.findClosestForm(this.element);
    }
    if (this.element) {
        // Add self to the DOM element
        this.element['fpValidator'] = this;
        // Bind the validate event for each event type
        for (var i = 0; i < this.config.events.length; i++) {
            this.element.addEventListener(this.config.events[i], function(event){
                var result = this['fpValidator'].validate();
                if (false === result) {
                    event.preventDefault();
                }
                return result;
            });
        }
    }

    // Convert constraits mapping to constraint objects for each getter
    this.getters = getters;
    for (var modelName in getters) {
        for (var methodName in getters[modelName]) {
            this.getters[modelName][methodName] = this.parseConstraints(getters[modelName][methodName]);
        }
    }
};

/**
 * Validate the form
 *
 * @returns {*}
 */
FpJsFormValidator.prototype.validate = function() {
    // Check if functional is enabled for this form
    if (false === this.config['isEnabled']) return true;

    var result = true;
    // Validate all the fields
    // This list was received with the "options" object from the server side
    // This is necessary to not go through all the elements in the form, but only those who are initially has constraints
    var fields = this.getConfig('trackingFields', []);
    for (var i = 0; i < fields.length; i++) {
        var element = document.getElementById(fields[i]);
        if (false === element[this.getName()].validate()) {
            result = false;
        }
    }

    // Validate global form models if has
    var errors = [];
    for (var modelName in this.getters) {
        if (typeof window[modelName] === 'function') {
            var model = new window[modelName]();
            for (var methodName in this.getters[modelName]) {
                var value = model[methodName]();
                var constraints = this.getters[modelName][methodName];
                for (var j = 0; j < constraints.length; j++) {
                    var constr = constraints[i];
                    constr.errors = [];
                    constr.validate(value);
                    errors = errors.concat(constr.errors);
                }
            }
        }
    }
    this.showErrors(errors);

    return 0 === errors.length;
};

/**
 * Fill out error-list with the received errors.
 *
 * @param {Array} errors
 */
FpJsFormValidator.prototype.showErrors = function(errors) {
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
FpJsFormValidator.prototype.getErrorListElement = function() {
    var ul = null;
    // Search for an error-list
    var children = Array.prototype.slice.call(this.element.children);
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
        this.element.insertBefore(ul, this.element.firstChild);
    }

    return ul;
};


/**
 * Convert constraints mapping to the real context objects
 *
 * @param {Object} mapping
 * @returns {Array}
 */
FpJsFormValidator.prototype.parseConstraints = function(mapping) {
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
 * Pass some external error list to show it in form
 * E.g. show errors received from ajax response
 *
 * @param errors
 */
FpJsFormValidator.prototype.showExternalErrors = function(errors) {
    for (var element_id in errors) {
        if (element_id == this.id) {
            this.showErrors(errors[element_id]);
        } else {
            var element = document.getElementById(element_id);
            if (undefined !== element['fpValidator']) {
                element['fpValidator'].showErrors(errors[element_id]);
            }
        }
    }
};
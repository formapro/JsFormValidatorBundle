/**
 * Created by ymaltsev on 11/21/13.
 */
function FpJsFormElement(options) {
    this.id                = null;
    this.name              = null;
    this.form              = null;
    this.dataClass         = null;
    this.invalidMessage    = null;
    this.type              = null;
    this.element           = null;
    this.parent            = null;
    this.cascadeValidation = false;
    this.validationData    = [];
    this.transformers      = [];
    this.events            = [];
    this.children          = {};
    this.errors            = [];
    this.requests          = [];
    this.config            = {};

    for (var name in options) {
        this[name] = options[name];
    }

    /**
     * Inititalize this model after construct
     *
     * @param {HTMLFormElement} form
     * @param {FpJsFormElement=} parent
     *
     * @returns {FpJsFormElement}
     */
    this.initialize = function(form, parent) {
        parent = parent ? parent : null;
        this.parent = parent;

        // Init the form element
        this.form = form;
        // Init the self element
        if (null === parent) {
            this.element = form;
        } else {
            this.element = document.getElementById(this.id);
        }
        // Convert transformers to their objects
        this.transformers = this.parseTransformers(this.transformers);
        // Init self validation data
        var i = this.validationData.length;
        while (i--) {
            this.validationData[i].initialize(this);
        }

        // Init children recursively
        for (var childName in this.children) {
            this.children[childName].initialize(form, this);
        }

        return this;
    };

    /**
     * @returns {string}
     */
    this.getId = function() {
        return this.id;
    };

    /**
     * @returns {string}
     */
    this.getName = function() {
        return this.name;
    };

    /**
     * @returns {HTMLFormElement}
     */
    this.getForm = function() {
        return this.form;
    };

    /**
     * @returns {HTMLElement}
     */
    this.getElement = function() {
        return this.element;
    };

    /**
     * Get data class of current model
     * Or search this value in parents
     *
     * @returns {string|null}
     */
    this.getDataClass = function() {
        if (this.dataClass) {
            return this.dataClass;
        } else if (this.getParent()) {
            return this.getParent().getDataClass();
        } else {
            return null;
        }
    };

    /**
     * Returns an invalid message
     * @returns {String}
     */
    this.getInvalidMessage = function() {
        return this.invalidMessage;
    };

    /**
     * @returns {String}
     */
    this.getType = function() {
        return this.type;
    };

    /**
     * Get the general form model
     *
     * @returns {FpJsFormElement}
     */
    this.getRoot = function() {
        if (null === this.getParent()) {
            return this;
        } else {
            return this.getParent().getRoot();
        }
    };

    /**
     * Config options
     *
     * @returns {{}}
     */
    this.getConfig = function() {
        return this.config;
    };

    /**
     * Add an ajax request to queue
     * All the requests will be added to the root model
     *
     * @param {String} url
     * @param {{}} data
     * @param {function} callback
     */
    this.addRequest = function(url, data, callback) {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            //IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            //IE6, IE5
            try { xmlhttp =  new ActiveXObject("Microsoft.XMLHTTP"); }  catch(e) {}
            try { xmlhttp =  new ActiveXObject("Msxml2.XMLHTTP"); }     catch(e) {}
            try { xmlhttp =  new ActiveXObject("Msxml2.XMLHTTP.6.0"); } catch(e) {}
            try { xmlhttp =  new ActiveXObject("Msxml2.XMLHTTP.3.0"); } catch(e) {}
        }

        if (undefined !== xmlhttp) {
            var serialize = function(obj, prefix) {
                var queryParts = [];
                for(var paramName in obj) {
                    var key = prefix
                        ? prefix + "[" + paramName + "]"
                        : paramName
                    ;
                    var child = obj[paramName];
                    queryParts.push(typeof child == "object" ?
                        serialize(child, key) :
                        encodeURIComponent(key) + "=" + encodeURIComponent(child));
                }
                return queryParts.join("&");
            };
            var paramsString = serialize(data, null);

            try {
                xmlhttp.open("POST", url, true);
                xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                this.getRoot().requests.push({
                    owner: this,
                    params: paramsString,
                    request: xmlhttp,
                    callback: callback
                });
            } catch (e) {}
        }
    };

    /**
     * Get count of requests which are in process
     *
     * @returns {number}
     */
    this.countProcessedRequests = function() {
        var cnt = 0;
        var i = this.requests.length;
        while (i--) if (4 !== this.requests[i].request.readyState) cnt++;
        return cnt;
    };

    //TODO: should be implemented
    /**
     * @returns {boolean}
     */
    this.hasValidConstraint = function() {
        return false;
    };

    /**
     * @returns FpJsFormElement[]
     */
    this.getChildren = function() {
        return this.children;
    };

    /**
     * @returns FpJsFormElement
     */
    this.getChild = function(name) {
        return this.children[name];
    };

    /**
     * @returns FpJsFormElement
     */
    this.getParent = function() {
        return this.parent;
    };

    /**
     * Get value of the current model due to its type
     *
     * @returns {*}
     */
    this.getValue = function() {
        var i = this.transformers.length;
        var value = this.getInputValue();

        if (i && undefined === value) {
            value = this.getMappedValue();
        } else {
            value = this.getSpecifiedElementTypeValue();
        }

        while (i--) {
            value = this.transformers[i].reverseTransform(value, this);
        }

        return value;
    };

    this.getInputValue = function() {
        return this.getElement()? this.getElement().value : undefined;
    };

    this.getMappedValue = function() {
        var result = this.getSpecifiedElementTypeValue();

        if (undefined === result) {
            result = {};
            for (var childName in this.getChildren()) {
                var child = this.getChild(childName);
                result[child.getName()] = child.getMappedValue();
            }
        }

        return result;
    };

    this.getSpecifiedElementTypeValue = function() {
        if (!this.getElement()) {
            return undefined;
        }

        var value;
        if ('checkbox' == this.getType() || 'radio' == this.getType()) {
            value = this.getElement().checked;
        } else if ('select' === this.getElement().tagName.toLowerCase()) {
            value = [];
            var field = this.getElement();
            var len = field.length;
            while (len--) {
                if (field.options[len].selected) {
                    value.push(field.options[len].value);
                }
            }
        } else {
            value = this.getInputValue();
        }

        return value;
    };

    /**
     * @returns {Array}
     */
    this.getErrors = function() {
        return this.errors;
    };

    /**
     * Accumulate all the form errors to a simplified object
     *
     * @param {{}=} holder
     * @returns {}
     */
    this.getMappedErrors = function(holder) {
        holder = holder ? holder : {};
        // Add self errors
        var errors = this.getErrors();
        if (errors.length) {
            holder[this.getId()] = {
                type: this.getType(),
                errors: errors
            };
        }
        // Add children errors recursively
        for (var childName in this.getChildren()) {
            holder = this.getChild(childName).getMappedErrors(holder);
        }

        return holder;
    };

    /**
     * @returns {boolean}
     */
    this.isCascade = function() {
        return this.cascadeValidation;
    };

    /**
     * Add some errors to the container
     *
     * @param {Array|String} errors
     *
     * @returns FpJsFormElement
     */
    this.addErrors = function(errors) {
        if (errors instanceof Array) {
            var i = errors.length;
            while (i--) {
                this.errors.push(errors[i]);
            }
        } else if (typeof errors == 'string') {
            this.errors.push(errors);
        }

        return this;
    };

    /**
     * Validate this model
     *
     * @returns {boolean}
     */
    this.isValid = function() {
        var value = this.getValue();

        if (null === this.element && undefined === value) {
            return true;
        }

        var i = this.validationData.length;
        while (i--) {
            this.validationData[i].validate(value);
        }

        return this.errors.length == 0;
    };

    /**
     *
     *
     * @param list
     * @returns {Array}
     */
    this.parseTransformers = function(list) {
        var result = [];
        var i = list.length;
        while (i--) {
            var transformer = this.createTransformer(list[i]);
            if (null !== transformer) {
                if (undefined !== transformer.transformers) {
                    transformer.transformers = this.parseTransformers(transformer.transformers);
                }
                result.push(transformer);
            }
        }

        return result;
    };

    this.createTransformer = function(item) {
        var name = String(item.name).replace(/\\/g, '');
        if (undefined !== window[name]) {
            var transformer = new window[name]();

            if (typeof transformer.reverseTransform !== 'function') {
                return null;
            }
            for (var propName in item) {
                transformer[propName] = item[propName];
            }

            return transformer;
        } else {
            return null;
        }
    }
}
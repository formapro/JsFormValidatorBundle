//noinspection JSUnusedGlobalSymbols
/**
 * Created by ymaltsev on 11/21/13.
 */
var FpJsFormValidatorFactory = new function() {
    this.forms = {};
    this.errorClass = 'form-error';

    //noinspection JSUnusedGlobalSymbols
    /**
     * The general function to init validation for a form
     *
     * @param {FpJsFormElement} model
     */
    this.initNewModel = function(model) {
        var self = this;

        this.onDocumentReady(function(){
            model.initialize(self.getFormDomElement(model));
            self.forms[model.getId()] = model;

            self.bindDefaultEvents(model);
            self.bindEvents(model);
        });
    };

    this.onDocumentReady = function(callback) {
        var addListener    = document.addEventListener || document.attachEvent;
        var removeListener = document.removeEventListener || document.detachEvent;
        var eventName      = document.addEventListener ? "DOMContentLoaded" : "onreadystatechange";

        addListener.call(document, eventName, function(){
            removeListener( eventName, arguments.callee, false );
            callback();
        }, false )
    };

    /**
     * Bind the specified events which were received from the server
     *
     * @param {FpJsFormElement} model
     */
    this.bindEvents = function(model) {
        for (var elementId in model.events) {
            var i;
            var element = document.getElementById(elementId);

            i = model.events[elementId].length;
            while (i--) {
                element.addEventListener(model.events[elementId][i], this.getEventCallback(model));
            }
        }

        for (var childName in model.children) {
            this.bindEvents(model.children[childName]);
        }
    };

    /**
     * This events is defined by default by this library
     * To prevent this events just redefine this function as empty fucntion
     *
     * @param {FpJsFormElement} model
     */
    this.bindDefaultEvents = function(model) {
        model.form.addEventListener('submit', this.getEventCallback(model));
    };

    /**
     * Create a callback that will ba called on the each event that requires the form validation
     *
     * @param {FpJsFormElement} model
     *
     * @returns {Function}
     */
    this.getEventCallback = function(model) {
        var self = this;
        return function(event){
            self.clearModel(model);

            var isValid = self.validateRecursively(model);
            var hasRequests = self.sendModelRequests(model);

            if (hasRequests || !isValid) {
                event.preventDefault();
            }

            if (!hasRequests) {
                self.getMethodAndShowErrors(model);
                self.postValidateEvent(model);
            }
        };
    };

    /**
     * Send all the exists ajax requests for the model
     *
     * @param {FpJsFormElement} model
     *
     * @returns {boolean}
     */
    this.sendModelRequests = function(model) {
        var len = model.requests.length;
        if (len) {
            for (var i = 0; i < len; i++) {
                var req = model.requests[i];
                req.request.onreadystatechange =
                    this.getXmlHttpRequestCallback(i, req.request, req.callback, model);

                req.request.send(req.params);
            }
            return true;
        } else {
            return false;
        }
    };

    /**
     * Create a major callback fucntion that will be called on each model's ajax response
     *
     * @param {String|Number} requestId
     * @param {XMLHttpRequest} request
     * @param {function} callback
     * @param {FpJsFormElement} model
     *
     * @returns {Function}
     */
    this.getXmlHttpRequestCallback = function(requestId, request, callback, model) {
        var self = this;
        return function() {
            if (4 == request.readyState && 200 == request.status) {
                callback(request.responseText, model.requests[requestId].owner);

                if (!model.countProcessedRequests()) {
                    self.getMethodAndShowErrors(model);
                    self.postValidateEvent(model);
                }
            }
        };
    };

    /**
     * Returns the global or local method which shows errors
     *
     * @param {FpJsFormElement} model
     */
    this.getMethodAndShowErrors = function(model) {
        if (undefined !== model.getForm().showErrors) {
            model.getForm().showErrors(model.getForm(), model.getMappedErrors());
        } else {
            this.showErrors(model.getForm(), model.getMappedErrors());
        }
    };

    /**
     * This event will be called after the synchronous or asynchronous form validation
     *
     * @param {FpJsFormElement} model
     */
    this.postValidateEvent = function(model) {
        var form = model.getForm();
        var errors = model.getMappedErrors();
        // Run the global event
        this.onvalidate(errors);
        // Run the local event for this form
        if (typeof form.onvalidate === 'function') {
            form.onvalidate(errors);
        }
    };

    /**
     * This event can be redefined to run some actions after form validation
     * This event calls globally for all the forms
     *
     * @param {{}} errors
     */
    this.onvalidate = function(errors) {};

    /**
     * Get the "from" DOM element due to model type
     *
     * @param {FpJsFormElement} model
     */
    this.getFormDomElement = function(model) {
        var id = model.id;
        var form = this.findClosestForm(document.getElementById(id));
        if (!form) {
            // Just get the first child name
            //noinspection LoopStatementThatDoesntLoopJS
            for(var childName in model.getChildren()) break;
            var childId = model.getChild(childName).getId();
            form = this.findClosestForm(document.getElementById(childId));
        }

        if (!form) {
            throw new Error('Can not find the form element with id="'+id+'"');
        } else {
            return form;
        }
    };

    /**
     * Find a closest "form" tag for specified DOM element
     *
     * @param {HTMLElement|Node} element
     *
     * @returns {HTMLElement|null}
     */
    this.findClosestForm = function(element) {
        if (element && 'form' === element.tagName.toLowerCase()) {
            return element;
        } else if (element && element.parentNode) {
            return this.findClosestForm(element.parentNode);
        } else {
            return null;
        }
    };

    /**
     * Validate the form model and all its children
     *
     * @param {FpJsFormElement} model
     */
    this.validateRecursively = function(model) {
        var isValid = model.isValid();
        var parent = model.getParent();
        for (var childName in model.getChildren()) {
            var child = model.getChild(childName);
            if (!parent || parent.isCascade() || !child.hasValidConstraint()) {
                isValid = !this.validateRecursively(child) ? false : isValid;
            }
        }

        return isValid;
    };

    /**
     * Show all the form errors on a page
     *
     * @param {HTMLFormElement} form
     * @param {{}} errors
     */
    this.showErrors = function(form, errors) {
        for (var elementId in errors) {
            var element = document.getElementById(elementId);
            if (null === element && 'form' === errors[elementId].type) {
                // Just get the first child's Id
                for (var childId in errors) if (elementId !== childId) break;
                // Looking for the form tag for this child
                element = this.findClosestForm(document.getElementById(childId));
            }
            var errorList = this.createErrorList(errors[elementId].errors);

            var parent = element;
            if (parent && 'form' !== errors[elementId].type) {
                //noinspection JSValidateTypes
                parent = element.parentNode;
            } else if (!parent) {
                parent = form;
            }

            // Remove existing errors
            var existList = parent.getElementsByTagName(errorList.tagName);
            var listLen = existList.length;
            while (listLen--) {
                if (errorList.className.toLowerCase() == existList[listLen].className.toLowerCase()) {
                    existList[listLen].parentNode.removeChild(existList[listLen]);
                }
            }
            // Show new errors
            parent.insertBefore(errorList, parent.firstChild);
        }
    };

    /**
     * Create an "ul" element and add there "li" elements with error messages
     *
     * @param {Array} errors
     *
     * @returns {HTMLElement}
     */
    this.createErrorList = function(errors) {
        var list = document.createElement('ul');
        list.className = this.errorClass;
        var i = errors.length;
        while (i--) {
            var li = document.createElement('li');
            li.innerHTML = errors[i];
            list.appendChild(li);
        }

        return list;
    };

    /**
     * Clear all the necessary data in a model (recursively)
     *
     * @param {FpJsFormElement} model
     *
     * @returns {FpJsFormValidatorFactory}
     */
    this.clearModel = function(model) {
        model.errors = [];
        model.requests = [];

        for (var childName in model.getChildren()) {
            this.clearModel(model.getChild(childName));
        }

        return this;
    }
}();
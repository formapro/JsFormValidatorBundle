//noinspection JSUnusedGlobalSymbols
var FpJsFormValidatorService = new function() {

    //noinspection JSUnusedGlobalSymbols
    this.addModel = function(model) {
        if (!model) return;
        this.onDocumentReady(function(){
            FpJsFormValidator.forms[model.id] = model;
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
     * @param {String} formId
     */
    this.bindEvents = function(formId) {
        element.addEventListener('submit', this.getEventCallback(model));
    };

    /**
     * Find a related "form" by id/name string
     *
     * @param {String} formId
     *
     * @returns {HTMLElement|null}
     */
    this.findRelatedForm = function(formId) {
        var element = document.getElementById(formId);

        if (!element) {
            element = document.getElementsByName(formId)[0];
        }
        if (!element) {
            throw new Error('FpJsFormValidator: Can not find a form by name="'+formId+'" or id="'+formId+'"');
        }

        if ('form' !== element.tagName.toLowerCase()) {
            return element;
        } else if (element && element.parentNode) {
            return this.findClosestForm(element.parentNode.id);
        } else {
            return null;
        }
    };

    this.searchFormRecursive = function(element) {
        if (element.parentNode && element.parentNode.tagName.to) {

        }
    }

}();
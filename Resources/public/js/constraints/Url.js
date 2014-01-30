//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value is like an URL address
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsUrl() {
    this.message = '';

    this.validate = function(value, element) {
        var errors = [];
        var regexp = /(ftp|https?):\/\/(www\.)?[\w\-\.@:%_\+~#=]+\.\w{2,3}(\/\w+)*(\?.*)?/;
        if (String(value).length > 0 && !regexp.test(value)) {
            element.domNode.value = 'http://' + value;
            errors.push(this.message.replace('{{ value }}', String('http://' + value)));
        }

        return errors;
    }
}

//noinspection JSUnusedGlobalSymbols
/**
 * Checks if value matches to the predefined regexp
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRegex() {
    this.message = '';
    this.pattern = '';
    this.match = true;

    this.validate = function(value) {
        var errors = [];
        if (String(value).length > 0 && !this.pattern.test(value)) {
            errors.push(this.message.replace('{{ value }}', String(value)));
        }

        return errors;
    };

    this.onCreate = function() {
        var flags = this.pattern.match(/[\/#](\w*)$/);
        this.pattern = new RegExp(this.pattern.trim().replace(/(^[\/#])|([\/#]\w*$)/g, ''), flags[1]);
    }
}

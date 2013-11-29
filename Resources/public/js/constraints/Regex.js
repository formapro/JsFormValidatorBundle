/**
 * Checks if value matches to the predefined regexp
 * @constructor
 * @author dev.ymalcev@gmail.com
 */
function SymfonyComponentValidatorConstraintsRegex() {
    this.pattern = '';
    this.match = true;

    this.validate = function(value) {
        return (String(value).length > 0 && !this.pattern.test(value))
            ? this.message.replace('{{ value }}', String(value))
            : null;
    };

    this.onCreate = function() {
        var flags = this.pattern.match(/\/(\w*)$/);
        this.pattern = new RegExp(this.pattern.trim().replace(/(^\/)|(\/\w*$)/g, ''), flags[1]);
    }
}

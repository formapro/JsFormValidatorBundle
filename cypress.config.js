const { defineConfig } = require('cypress');

module.exports = defineConfig({
    allowCypressEnv: false,
    e2e: {
        baseUrl: 'http://webserver',
        specPattern: 'cypress/integration/**/*.js',
        supportFile: false,
    },
});

const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/Browser',
  use: {
    baseURL: 'http://127.0.0.1:8000',
  },
});



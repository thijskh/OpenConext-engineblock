const puppeteer = require('puppeteer-core');
const mkdirp = require('mkdirp');
const path = require('path');
const fs = require('fs');
const os = require('os');

const DIR = path.join(os.tmpdir(), 'jest_puppeteer_global_setup');

module.exports = async function() {
    const args = [
        '--no-sandbox',
        '--ignore-certificate-errors'
    ];
    const options = {
        executablePath: '/usr/bin/google-chrome',
        args,
        ignoreHTTPSErrors: true
    };
    const browser = await puppeteer.launch(options);
    // store the browser instance so we can teardown it later
    // this global is only available in the teardown but not in TestEnvironments
    global.__BROWSER_GLOBAL__ = browser;

    // use the file system to expose the wsEndpoint for TestEnvironments
    mkdirp.sync(DIR);
    fs.writeFileSync(path.join(DIR, 'wsEndpoint'), browser.wsEndpoint());
};

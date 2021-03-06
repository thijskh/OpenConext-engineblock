const timeout = 15000;
const {toMatchImageSnapshot} = require('jest-image-snapshot');

// Extend Jest expect
expect.extend({toMatchImageSnapshot});

const errorPages = [
    {
        name: 'unable-to-receive-message',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unable-to-receive-message&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98", "serviceProvider":"https://current-sp.entity-id.org/metadata","serviceProviderName":"OpenConext Drop Supplies SP"}'
    },
    {
        name: 'session-lost',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=session-lost'
    },
    {
        name: 'session-not-started',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=session-not-started'
    },
    {
        name: 'no-idps',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=no-idps&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName":"OpenConext Drop Supplies SP"}'
    },
    {
        name: 'invalid-acs-location',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=invalid-acs-location&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName":"OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'unsupported-acs-location-scheme',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unsupported-acs-location-scheme&feedback-info={"statusCode": "418", "statusMessage": "(No message provided)", "requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'missing-required-fields',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=missing-required-fields&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98", "serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'invalid-acs-binding',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=invalid-acs-binding&feedback-info={"serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'received-error-status-code',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=received-error-status-code'
    },
    {
        name: 'received-invalid-signed-response',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=received-invalid-signed-response&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'received-invalid-response',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=received-invalid-response&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'no-consent',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=no-consent&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'unknown-service',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unknown-service'
    },
    {
        name: 'unknown-preselected-idp',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unknown-preselected-idp&feedback-info={"Idp Hash": "64531cc179d0d2e66243c30e58125f0a", "requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP"}'
    },
    {
        name: 'stuck-in-authentication-loop',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=stuck-in-authentication-loop&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'clock-issue',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=clock-issue&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata", "serviceProviderName": "OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    },
    {
        name: 'unknown-issuer',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unknown-issuer&feedback-info={"requestId":"5cb4bd3879b49","ipAddress":"192.168.66.98","artCode":"31914","EntityId":"https://serviceregistry.vm.openconext.org/simplesaml/module.php/saml/sp/metadata.php/default-sp","Destination":"https://serviceregistry.vm.openconext.org/simplesaml/module.php/saml/sp/metadata.php/destination"}'
    },
    {
        name: 'unsupported-signature-method',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unsupported-signature-method&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata","serviceProviderName":"OpenConext Drop Supplies SP"}&parameters={"signatureMethod":"http://www.w3.org/2000/09/xmldsig%23%0Arsa-sha1"}'
    },
    {
        name: 'unknown-service-provider',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=unknown-service-provider&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata","serviceProviderName":"OpenConext Drop Supplies SP"}&parameters={"entityId":"https://serviceregistry.vm.openconext.org/simplesaml/module.php/saml/sp/metadata.php/default-sp"}'
    },
    {
        name: 'invalid-attribute-value',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=invalid-attribute-value&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata","serviceProviderName":"OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}&parameters={"attributeName":"schacHomeOrganization","attributeValue":"openconext"}'
    },
    {
        name: 'no-authentication-request-received',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=no-authentication-request-received&parameters={"message":"No SAMLRequest parameter was found in the HTTP POST request parameters"}'
    },
    {
        name: 'authorization-policy-violation',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=authorization-policy-violation&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata","serviceProviderName":"OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}&parameters={"logo":{"height":"96","width":"96","url":"https://static.vm.openconext.org/media/conext_logo.png"},"policyDecisionMessage":"No localized deny messages present"}'
    },
    {
        name: 'uncaught-error',
        url: 'https://engine.vm.openconext.org/functional-testing/feedback?template=feedback_unknown_error&feedback-info={"requestId":"5cb4bd3879b49","artCode":"31914", "ipAddress":"192.168.66.98","serviceProvider":"https://current-sp.entity-id.org/metadata","serviceProviderName":"OpenConext Drop Supplies SP","identityProvider":"https://current-idp.entity-id.org/metadata"}'
    }
];

const viewports = [
    {width: 375, height: 667},
    {width: 1920, height: 1080},
];

describe(
    'Verify',
    () => {
        let page;

        beforeAll(async () => {
            page = await global.__BROWSER__.newPage();
            jest.setTimeout(20000);
        }, timeout);

        for (const errorPage of errorPages) {
            for (const viewport of viewports) {
                it(`${errorPage.name}-${viewport.width}x${viewport.height}`, async (page, expect) => {

                    await page.goto(errorPage.url);
                    await page.setViewport(viewport);
                    await page.waitFor(".error-container");
                    const screenshot = await page.screenshot({
                        fullPage: true,
                        path: `./material/javascripts/tests/visual-regression/error-page/screenshots/error-page/${errorPage.name}-${viewport.width}x${viewport.height}.png`
                    });
                    expect(screenshot).toMatchImageSnapshot();
                });
            }
        }
    },
    timeout,
);

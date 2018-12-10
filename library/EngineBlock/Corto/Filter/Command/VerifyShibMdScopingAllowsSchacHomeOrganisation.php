<?php

use OpenConext\EngineBlock\Metadata\ShibMdScope;
use OpenConext\Value\Saml\Metadata\ShibbolethMetadataScope;
use OpenConext\Value\Saml\Metadata\ShibbolethMetadataScopeList;
use Psr\Log\LoggerInterface;

class EngineBlock_Corto_Filter_Command_VerifyShibMdScopingAllowsSchacHomeOrganisation extends
    EngineBlock_Corto_Filter_Command_Abstract
{
    const SCHAC_HOME_ORGANIZATION_URN_MACE = 'urn:mace:terena.org:attribute-def:schacHomeOrganization';
    const SCHAC_HOME_ORGANIZATION_URN_OID  = 'urn:oid:1.3.6.1.4.1.25178.1.2.9';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $blockUserOnViolation;

    public function __construct(LoggerInterface $logger, $blockUserOnViolation)
    {
        $this->logger = $logger;
        $this->blockUserOnViolation = (bool)$blockUserOnViolation;
    }

    /**
     * @throws EngineBlock_Corto_Exception_InvalidAttributeValue
     */
    public function execute()
    {
        $this->logger->info('Verifying if schacHomeOrganization is allowed by configured IdP shibmd:scopes');

        $scopes = $this->_identityProvider->shibMdScopes;
        if (empty($scopes)) {
            $this->logger->notice('No shibmd:scope found in the IdP metadata, not verifying schacHomeOrganization');

            return;
        }

        $schacHomeOrganization = $this->resolveSchacHomeOrganization();
        if ($schacHomeOrganization === false) {
            $this->logger->notice('No schacHomeOrganization found in response, not verifying');

            return;
        }

        $scopeList = $this->buildScopeList($scopes);

        if (!$scopeList->inScope($schacHomeOrganization)) {
            $message = sprintf(
                'schacHomeOrganization attribute value "%s" is not allowed by configured ShibMdScopes for IdP "%s"',
                $schacHomeOrganization, $this->_identityProvider->entityId
            );

            $this->logger->warning($message);

            if ($this->blockUserOnViolation) {
                throw new EngineBlock_Corto_Exception_InvalidAttributeValue($message, 'schacHomeOrganization', $schacHomeOrganization);
            }
        }
    }

    /**
     * @return string|false
     */
    private function resolveSchacHomeOrganization()
    {
        $attributes = $this->_response->getAssertion()->getAttributes();

        if (isset($attributes[self::SCHAC_HOME_ORGANIZATION_URN_MACE])) {
            return reset($attributes[self::SCHAC_HOME_ORGANIZATION_URN_MACE]);
        } else {
            $this->logger->debug('No schacHomeOrganization attribute found using urn:mace');
        }

        if (isset($attributes[self::SCHAC_HOME_ORGANIZATION_URN_OID])) {
            return reset($attributes[self::SCHAC_HOME_ORGANIZATION_URN_OID]);
        } else {
            $this->logger->debug('No schacHomeOrganization attribute found using urn:oid');
        }

        return false;
    }

    /**
     * @param ShibMdScope[] $scopes
     * @return ShibbolethMetadataScopeList
     */
    private function buildScopeList(array $scopes)
    {
        $scopes = array_map(
            function (ShibMdScope $scope) {
                if (!$scope->regexp) {
                    return ShibbolethMetadataScope::literal($scope->allowed);
                }

                return ShibbolethMetadataScope::regexp($scope->allowed);
            },
            $scopes
        );

        return new ShibbolethMetadataScopeList($scopes);
    }
}

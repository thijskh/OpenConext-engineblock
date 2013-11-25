<?php

/**
 * Class EngineBlock_Corto_Mapper_Legacy_ResponseTranslator
 */
class EngineBlock_Corto_Mapper_Legacy_ResponseTranslator
{
    /**
     * @var array
     */
    protected $privateVars = array(
        'Return',
        'OriginalIssuer',
        'OriginalNameId',
        'OriginalBinding',
        'OriginalResponse',
        'CollabPersonId',
        'CustomNameId',
        'IntendedNameId',
    );

    /**
     * @param EngineBlock_Saml2_ResponseAnnotationDecorator $response
     * @return array
     */
    public function fromNewFormat(EngineBlock_Saml2_ResponseAnnotationDecorator $response)
    {
        $legacyResponse = EngineBlock_Corto_XmlToArray::xml2array(
            $response->getSspMessage()->toUnsignedXML()->ownerDocument->saveXML()
        );

        return $this->addPrivateVarsToLegacy($legacyResponse, $response);
    }

    /**
     * @param array $legacyResponse
     * @return EngineBlock_Saml2_ResponseAnnotationDecorator
     */
    public function fromOldFormat(array $legacyResponse)
    {
        $xml = EngineBlock_Corto_XmlToArray::array2xml($legacyResponse);
        $document = new DOMDocument();
        $document->loadXML($xml);

        $response = new SAML2_Response($document->firstChild);
        $annotatedResponse = new EngineBlock_Saml2_ResponseAnnotationDecorator($response);

        return $this->addPrivateVars($annotatedResponse, $legacyResponse);
    }

    /**
     * @param array $to
     * @param EngineBlock_Saml2_ResponseAnnotationDecorator $from
     * @return array
     */
    protected function addPrivateVarsToLegacy(array $to, EngineBlock_Saml2_ResponseAnnotationDecorator $from)
    {
        if (!isset($to[EngineBlock_Corto_XmlToArray::PRIVATE_PFX])) {
            $to[EngineBlock_Corto_XmlToArray::PRIVATE_PFX] = array();
        }

        foreach ($this->privateVars as $privateVar) {
            $method = 'get' . $privateVar;
            $value = $from->$method();
            if ($value) {
                $to[EngineBlock_Corto_XmlToArray::PRIVATE_PFX][$privateVar] = $value;
            }
        }
        return $to;
    }

    /**
     * @param EngineBlock_Saml2_ResponseAnnotationDecorator $to
     * @param array $from
     * @return EngineBlock_Saml2_ResponseAnnotationDecorator
     */
    protected function addPrivateVars(EngineBlock_Saml2_ResponseAnnotationDecorator $to, array $from)
    {
        foreach ($this->privateVars as $privateVar) {
            if (isset($to[EngineBlock_Corto_XmlToArray::PRIVATE_PFX][$privateVar])) {
                $method = 'set' . $privateVar;
                $to->$method($to[EngineBlock_Corto_XmlToArray::PRIVATE_PFX][$privateVar]);
            }
        }
        return $to;
    }
}
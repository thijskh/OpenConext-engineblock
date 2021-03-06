<?php

use OpenConext\EngineBlock\Metadata\Entity\ServiceProvider;

class EngineBlock_Corto_Mapper_Metadata_Entity_SpSsoDescriptor_AttributeConsumingService_RequestedAttributes
{
    /**
     * @var ServiceProvider
     */
    private $_entity;

    public function __construct(ServiceProvider $entity)
    {
        $this->_entity = $entity;
    }

    public function mapTo(array $rootElement)
    {
        if (!isset($this->_entity->requestedAttributes)) {
            return $rootElement;
        }
        $rootElement['md:RequestedAttribute'] = array();
        foreach ($this->_entity->requestedAttributes as $requestedAttribute) {
            $element = array();

            $ATTRIBUTE_PREFIX = EngineBlock_Corto_XmlToArray::ATTRIBUTE_PFX;
            $element[$ATTRIBUTE_PREFIX . 'Name']       = $requestedAttribute->name;
            $element[$ATTRIBUTE_PREFIX . 'NameFormat'] = $requestedAttribute->nameFormat;
            $element[$ATTRIBUTE_PREFIX . 'isRequired'] = $requestedAttribute->required ? 'true' : 'false';

            $rootElement['md:RequestedAttribute'][] = $element;
        }
        return $rootElement;
    }
}

<?php

namespace OpenConext\EngineBlockBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use OpenConext\EngineBlock\Authentication\Value\CollabPersonUuid;
use OpenConext\EngineBlock\Exception\InvalidArgumentException;

class CollabPersonUuidType extends Type
{
    const NAME = 'engineblock_collab_person_uuid';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getGuidTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return $value;
        }

        if (!$value instanceof CollabPersonUuid) {
            throw new ConversionException(
                sprintf(
                    'Value "%s" must be null or an instance of CollabPersonUuid to be able to ' .
                    'convert it to a database value',
                    is_object($value) ? get_class($value) : (string)$value
                )
            );
        }

        return $value->getUuid();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return $value;
        }

        try {
            $collabPersonUuid = new CollabPersonUuid($value);
        } catch (InvalidArgumentException $e) {
            // get nice standard message, so we can throw it keeping the exception chain
            $doctrineExceptionMessage = ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                'valid UUIDv4'
            )->getMessage();

            throw new ConversionException($doctrineExceptionMessage, 0, $e);
        }

        return $collabPersonUuid;
    }

    public function getName()
    {
        return self:: NAME;
    }
}

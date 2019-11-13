<?php


namespace Lukasz93P\objectSerializer;


use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader('class_exists');

final class ObjectSerializerFactory
{
    public static function create(array $objectIdentificationKeysToClassNamesMapping): ObjectSerializer
    {
        return JsonObjectSerializer::create($objectIdentificationKeysToClassNamesMapping);
    }

}
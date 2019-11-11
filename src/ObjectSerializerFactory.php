<?php


namespace Lukasz93P\objectSerializer;


final class ObjectSerializerFactory
{
    public static function create(array $objectIdentificationKeysToClassNamesMapping): ObjectSerializer
    {
        return JsonObjectSerializer::create($objectIdentificationKeysToClassNamesMapping);
    }

}
<?php
declare(strict_types=1);


namespace Lukasz93P\objectSerializer;


use Lukasz93P\objectSerializer\exceptions\SerializedObjectCorrupted;
use Lukasz93P\objectSerializer\exceptions\SerializedObjectIdentificationKeyNotMappedToClass;

interface ObjectSerializer
{
    public function serialize(SerializableObject $serializableObject): string;

    /**
     * @param string $serializedObject
     * @return SerializableObject
     * @throws SerializedObjectCorrupted
     * @throws SerializedObjectIdentificationKeyNotMappedToClass
     */
    public function deserialize(string $serializedObject): SerializableObject;
}
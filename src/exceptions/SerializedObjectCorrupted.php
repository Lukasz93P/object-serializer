<?php
declare(strict_types=1);


namespace Lukasz93P\objectSerializer\exceptions;


class SerializedObjectCorrupted extends DeserializationFailed
{
    public static function fromSerializedObject(string $serializedObject): self
    {
        return new self('Serialized object has been corrupted and cannot be deserialized. Serialized body:' . PHP_EOL . $serializedObject);
    }

}
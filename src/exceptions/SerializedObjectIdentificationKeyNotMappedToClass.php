<?php
declare(strict_types=1);


namespace Lukasz93P\objectSerializer\exceptions;


class SerializedObjectIdentificationKeyNotMappedToClass extends DeserializationFailed
{
    public static function fromIdentificationKey(string $identificationKey): self
    {
        return new self("Class mapping for serialized object with identification key: $identificationKey not found.");
    }

}
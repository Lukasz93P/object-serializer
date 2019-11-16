<?php
declare(strict_types=1);


namespace Lukasz93P\objectSerializer;


use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Lukasz93P\objectSerializer\exceptions\SerializedObjectCorrupted;
use Lukasz93P\objectSerializer\exceptions\SerializedObjectIdentificationKeyNotMappedToClass;

class JsonObjectSerializer implements ObjectSerializer
{
    private const SERIALIZED_OBJECT_CLASS_IDENTIFICATION_KEY_INDEX = 'serializedObjectClassIdentificationKey';
    private const SERIALIZED_OBJECT_DATA_INDEX                     = 'serializedObjectData';

    protected const SERIALIZATION_FORMAT = 'json';

    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * @var array
     */
    private $objectIdentificationKeysToClassNamesMapping;

    public static function create(array $objectIdentificationKeysToClassNamesMapping): self
    {
        return new self(SerializerBuilder::create()->build(), $objectIdentificationKeysToClassNamesMapping);
    }

    private function __construct(SerializerInterface $serializer, array $objectIdentificationKeysToClassNamesMapping)
    {
        $this->jmsSerializer = $serializer;
        $this->objectIdentificationKeysToClassNamesMapping = $objectIdentificationKeysToClassNamesMapping;
    }

    public function serialize(SerializableObject $serializableObject): string
    {
        return json_encode(
            [
                self::SERIALIZED_OBJECT_CLASS_IDENTIFICATION_KEY_INDEX => $serializableObject->classIdentificationKey(),
                self::SERIALIZED_OBJECT_DATA_INDEX                     => $this->jmsSerializer->serialize($serializableObject, self::SERIALIZATION_FORMAT),
            ]
        );
    }

    public function deserialize(string $serializedObject): SerializableObject
    {
        $decodedObjectJson = json_decode($serializedObject, true);
        if ($this->isDecodedObjectCorrupted($decodedObjectJson)) {
            throw SerializedObjectCorrupted::fromSerializedObject($serializedObject);
        }
        $decodedObjectClassName = $this->getObjectClassNameByIdentificationKey($decodedObjectJson[self::SERIALIZED_OBJECT_CLASS_IDENTIFICATION_KEY_INDEX]);

        return $this->jmsSerializer->deserialize(
            $decodedObjectJson[self::SERIALIZED_OBJECT_DATA_INDEX],
            $decodedObjectClassName,
            self::SERIALIZATION_FORMAT
        );
    }

    private function isDecodedObjectCorrupted(array $decodedObjectJson): bool
    {
        return empty($decodedObjectJson[self::SERIALIZED_OBJECT_CLASS_IDENTIFICATION_KEY_INDEX]) || empty($decodedObjectJson[self::SERIALIZED_OBJECT_DATA_INDEX]);
    }

    /**
     * @param string $identificationKey
     * @return string
     * @throws SerializedObjectIdentificationKeyNotMappedToClass
     */
    private function getObjectClassNameByIdentificationKey(string $identificationKey): string
    {
        if (!array_key_exists($identificationKey, $this->objectIdentificationKeysToClassNamesMapping)) {
            throw SerializedObjectIdentificationKeyNotMappedToClass::fromIdentificationKey($identificationKey);
        }

        return $this->objectIdentificationKeysToClassNamesMapping[$identificationKey];
    }

}
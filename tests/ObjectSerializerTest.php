<?php
declare(strict_types=1);


namespace tests;


use JMS\Serializer\Annotation as Serializer;
use Lukasz93P\objectSerializer\exceptions\SerializedObjectCorrupted;
use Lukasz93P\objectSerializer\exceptions\SerializedObjectIdentificationKeyNotMappedToClass;
use Lukasz93P\objectSerializer\ObjectSerializerFactory;
use Lukasz93P\objectSerializer\SerializableObject;
use PHPUnit\Framework\TestCase;

class TestSerializableClass implements SerializableObject
{
    public const IDENTIFICATION_KEY = 'test';

    /**
     * @var string
     * @Serializer\SerializedName("testStringField")
     * @Serializer\Type("string")
     */
    private $testStringField;

    /**
     * @var int
     * @Serializer\SerializedName("testNumericField")
     * @Serializer\Type("int")
     */
    private $testNumericField;

    public function __construct(string $testStringField, int $testNumericField)
    {
        $this->testStringField = $testStringField;
        $this->testNumericField = $testNumericField;
    }

    public function classIdentificationKey(): string
    {
        return self::IDENTIFICATION_KEY;
    }

    public function getTestStringField(): string
    {
        return $this->testStringField;
    }

    public function getTestNumericField(): int
    {
        return $this->testNumericField;
    }

}

class ObjectSerializerTest extends TestCase
{
    private $objectSerializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectSerializer = ObjectSerializerFactory::create([TestSerializableClass::IDENTIFICATION_KEY => TestSerializableClass::class]);
    }


    public function testShouldSerializeAndDeserializeSerializableObjectInstance(): void
    {
        $serializableObjectInstance = new TestSerializableClass('test', 1);

        $serializedObject = $this->objectSerializer->serialize($serializableObjectInstance);
        $deserializedObject = $this->objectSerializer->deserialize($serializedObject);

        $this->assertInstanceOf(TestSerializableClass::class, $deserializedObject);
    }

    public function testShouldSerializeAndDeserializeSerializableObjectInstanceWithClassFieldsRegardlessOfThoseFieldsVisibilityAndWithoutAvailableSetters(
    ): void
    {
        $testStringValue = 'testStringValue';
        $testNumericValue = 4235;
        $serializableObjectInstance = new TestSerializableClass($testStringValue, $testNumericValue);

        $serializedObject = $this->objectSerializer->serialize($serializableObjectInstance);
        /** @var TestSerializableClass $deserializedObject */
        $deserializedObject = $this->objectSerializer->deserialize($serializedObject);

        $this->assertEquals($testStringValue, $deserializedObject->getTestStringField());
        $this->assertEquals($testNumericValue, $deserializedObject->getTestNumericField());
    }

    public function testShouldThrowSerializableObjectCorruptedExceptionIfSerializedObjectDoesNotHaveRequiredFormat(): void
    {
        $this->expectException(SerializedObjectCorrupted::class);
        $this->objectSerializer->deserialize('{"someKey":"someValue", "data":{}}');
    }

    public function testShouldThrowSerializedObjectIdentificationKeyNotMappedToClassWhenMappingForSerializedObjectWasNotProvidedDuringSerializerConstruction(
    ): void
    {
        $serializedObject = $this->objectSerializer->serialize(
            new class implements SerializableObject
            {
                public function classIdentificationKey(): string
                {
                    return 'some unmapped key';
                }

            }
        );

        $this->expectException(SerializedObjectIdentificationKeyNotMappedToClass::class);
        $this->objectSerializer->deserialize($serializedObject);
    }

}
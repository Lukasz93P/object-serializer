# object-serializer

Current implementation uses **jms/serializer**:
https://packagist.org/packages/jms/serializer

So **jms/serializer**'s annotations are a must for this package - here You can read about them:
http://jmsyst.com/libs/serializer/master/reference/annotations

## Why to use this package?
- It gives You very easy to use tool to convert php objects into JSON and inversely.
- Public properties, constructor or setters are not required so converted objects can utilize full encapsulation.
- Native php serialization is not used so serialized objects are not language dependent.
- Supports very rich spectrum off properties types (including class instances).

## Hot to use?
1 Class which You want to serialize have to implement 
```php
<?php


namespace Lukasz93P\objectSerializer;


interface SerializableObject
{
    /**
    * Unique identifier(per class, not per instance) used to identify serialized object class
    * Event if You change class name/it's namespace after object serialization it still can be deserialized properly
    **/
    public function classIdentificationKey(): string;
}
```
2 Add annotations to class implementing SerializableObjects like it's described in **jms/serializer**
documentation: http://jmsyst.com/libs/serializer/master/reference/annotations.

3 Serialize class instance:
```php
$serializer = ObjectSerializerFactory::create([]);
$objectSerializedToJson = $serializer->serialize($serializableObjectInstance);
```

4 Deserialize class instance:
```php
$serializer = ObjectSerializerFactory::create([TestSerializableClass::IDENTIFICATION_KEY => TestSerializableClass::class]);
$testSerializableClassInstace = $serializer->deserialize($serializedTestSerializableClassInstance);
```

5 Instantiation of ```ObjectSerializer```:
You should do this through ```ObjectSerializerFactory::create``` method. That method receives associative array
which is used as mapping between result of ```SerializableObject::classIdentificationKey``` and fully qualified class name for each
class implementing ```SerializableObject```.

## Example
```php
<?php


namespace tests;


use JMS\Serializer\Annotation as Serializer;
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

}
```


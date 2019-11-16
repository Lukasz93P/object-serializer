<?php
declare(strict_types=1);


namespace Lukasz93P\objectSerializer;


interface SerializableObject
{
    public function classIdentificationKey(): string;
}
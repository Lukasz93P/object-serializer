<?php


namespace Lukasz93P\objectSerializer;


interface SerializableObject
{
    public function classIdentificationKey(): string;
}
<?php

namespace Opsource\QueryAdapter\Traits;

use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use ReflectionClass;
use ReflectionProperty;

trait Serializesr
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * Prepare the instance values for serialization.
     *
     * @return array
     */
    public function __serialize()
    {
        serialize($this->adapter);
//        $values = [];
//
//        $reflectionClass = new ReflectionClass($this);
//
//        [$class, $properties, $classLevelWithoutRelations] = [
//            get_class($this),
//            $reflectionClass->getProperties(),
//            !empty($reflectionClass->getAttributes(WithoutRelations::class)),
//        ];
//
//        foreach ($properties as $property) {
//            if ($property->isStatic()) {
//                continue;
//            }
//
//            if (!$property->isInitialized($this)) {
//                continue;
//            }
//
//            $value = $this->getPropertyValue($property);
//
//            if ($property->hasDefaultValue() && $value === $property->getDefaultValue()) {
//                continue;
//            }
//
//            $name = $property->getName();
//
//            if ($property->isPrivate()) {
//                $name = "\0{$class}\0{$name}";
//            } elseif ($property->isProtected()) {
//                $name = "\0*\0{$name}";
//            }
//
//            $values[$name] = $this->getSerializedPropertyValue(
//                $value,
//                !$classLevelWithoutRelations &&
//                empty($property->getAttributes(WithoutRelations::class))
//            );
//        }
//
//        return $values;
    }

    /**
     * Restore the model after serialization.
     *
     * @param  array  $values
     * @return void
     */
    public function __unserialize(array $values)
    {
        return unserialize($this->adapter);
//        $properties = (new ReflectionClass($this))->getProperties();
//
//        $class = get_class($this);
//
//        foreach ($properties as $property) {
//            if ($property->isStatic()) {
//                continue;
//            }
//
//            $name = $property->getName();
//
//            if ($property->isPrivate()) {
//                $name = "\0{$class}\0{$name}";
//            } elseif ($property->isProtected()) {
//                $name = "\0*\0{$name}";
//            }
//
//            if (!array_key_exists($name, $values)) {
//                continue;
//            }
//
//            $property->setValue(
//                $this,
//                $this->getRestoredPropertyValue($values[$name])
//            );
//        }
    }

    /**
     * Get the property value for the given property.
     *
     * @param  \ReflectionProperty  $property
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property)
    {
        return $property->getValue($this);
    }
}

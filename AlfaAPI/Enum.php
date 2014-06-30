<?php
/**
 * Created by bafoed.
 * URL: http://bafoed.net
 *
 * This code is not owned by, is not licensed by nor is a subsidiary of OJSC Alfa-Bank.
 */
namespace AlfaAPI;
abstract class Enum
{

    private $_value;

    protected function __construct($value)
    {
        $this->_value = $value;
    }

    public function __toString()
    {
        return (string)$this->_value;
    }

    public static function enumerate()
    {
        $class = get_called_class();
        $ref = new \ReflectionClass($class);
        $statics = $ref->getStaticProperties();
        foreach ($statics as $name => $value) {
            $ref->setStaticPropertyValue($name, new $class($value));
        }
    }
}

<?php
abstract class OS_Entity
{

    protected $_id;
    
    public function __construct (array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function setOptions (array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function __set ($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || ! method_exists($this, $method)) {
            throw new Exception('Invalid entity property: '.$name);
        }
        $this->$method($value);
    }

    public function __get ($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || ! method_exists($this, $method)) {
            throw new Exception('Invalid entity property: '.$name);
        }
        return $this->$method();
    }
	
	/**
     * @return the $_id
     */
    public function getId ()
    {
        return $this->_id;
    }

	/**
     * @param field_type $id
     */
    public function setId ($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * 
     * @return multitype:unknown
     */
    public function toArray()
    {
        $properties = array();
        foreach ( get_object_vars($this) as $property => $value ) {
            $properties[substr($property, 1)] = $value;
        }
        return $properties;
    }

}


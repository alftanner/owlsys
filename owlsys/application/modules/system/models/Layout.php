<?php

class System_Model_Layout extends OS_Entity
{

    protected $_name;
    protected $_description;
    protected $_isPublished;
	/**
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

	/**
     * @return the $_description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

	/**
     * @return the $_published
     */
    public function getIsPublished ()
    {
        return $this->_isPublished;
    }

	/**
     * @param field_type $name
     */
    public function setName ($name)
    {
        $this->_name = $name;
        return $this;
    }

	/**
     * @param field_type $description
     */
    public function setDescription ($description)
    {
        $this->_description = $description;
        return $this;
    }

	/**
     * @param field_type $published
     */
    public function setIsPublished ($published)
    {
        $this->_isPublished = $published;
        return $this;
    }
    

}



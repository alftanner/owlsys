<?php

class System_Model_Skin extends OS_Entity
{

    protected $_name;
    protected $_description;
    protected $_isSelected;
    protected $_author;
    protected $_license;
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
     * @return the $_isSelected
     */
    public function getIsSelected ()
    {
        return $this->_isSelected;
    }

	/**
     * @return the $_author
     */
    public function getAuthor ()
    {
        return $this->_author;
    }

	/**
     * @return the $_license
     */
    public function getLicense ()
    {
        return $this->_license;
    }

	/**
     * @param field_type $_name
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

	/**
     * @param field_type $_description
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
        return $this;
    }

	/**
     * @param field_type $_isSelected
     */
    public function setIsSelected ($_isSelected)
    {
        $this->_isSelected = $_isSelected;
        return $this;
    }

	/**
     * @param field_type $_author
     */
    public function setAuthor ($_author)
    {
        $this->_author = $_author;
        return $this;
    }

	/**
     * @param field_type $_license
     */
    public function setLicense ($_license)
    {
        $this->_license = $_license;
        return $this;
    }

    
    
	
}


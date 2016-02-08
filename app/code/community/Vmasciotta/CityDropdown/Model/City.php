<?php

/**
 * Class Vmasciotta_CityDropdown_Model_City
 *
 * City
 *
 * @method Vmasciotta_CityDropdown_Model_Resource_City _getResource()
 * @method Vmasciotta_CityDropdown_Model_Resource_City getResource()
 *
 * @package     Vmasciotta_CityDropdown
 * @author      Valerio Masciotta <sviluppo@valeriomasciotta.it>
 */
class Vmasciotta_CityDropdown_Model_City extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('vmasciotta_citydropdown/city');
    }

    /**
     * Retrieve region name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getData('name');
        if (is_null($name)) {
            $name = $this->getData('default_name');
        }

        return $name;
    }

    public function loadByName($name, $regionId)
    {
        $this->_getResource()->loadByName($this, $name, $regionId);
        return $this;
    }

}
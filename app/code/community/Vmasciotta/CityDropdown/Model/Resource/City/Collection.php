<?php

class Vmasciotta_CityDropdown_Model_Resource_City_Collection extends  Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_regionTable;
    protected $_regionCityNameTable;

    protected function _construct()
    {
        $this->_init('vmasciotta_citydropdown/city');

        $this->_regionTable = $this->getTable('directory/country_region');
        $this->_regionCityNameTable = $this->getTable('vmasciotta_citydropdown/country_region_city_name');

        $this->addOrder('name', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', Varien_Data_Collection::SORT_ORDER_ASC);
    }

    /**
     * Initialize select object
     *
     * @return Vmasciotta_CityDropdown_Model_Resource_City_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = Mage::app()->getLocale()->getLocaleCode();

        $this->addBindParam(':city_locale', $locale);
        $this->getSelect()->joinLeft(
            array('rname' => $this->_regionCityNameTable),
            'main_table.city_id = rname.city_id AND rname.locale = :city_locale',
            array('name'));

        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_toOptionArray('city_id', 'default_name', array('title' => 'default_name'));
        if (count($options) > 0) {
            array_unshift($options, array(
                'title '=> null,
                'value' => "",
                'label' => Mage::helper('directory')->__('-- Please select --')
            ));
        }
        return $options;
    }
}
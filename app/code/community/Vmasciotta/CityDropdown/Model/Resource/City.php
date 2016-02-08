<?php

class Vmasciotta_CityDropdown_Model_Resource_City extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Table with localized region names
     *
     * @var string
     */
    protected $_cityNameTable;

    /**
     * Define main and locale region name tables
     *
     */
    protected function _construct()
    {
        $this->_init('vmasciotta_citydropdown/country_region_city','city_id');
        $this->_cityNameTable = $this->getTable('vmasciotta_citydropdown/country_region_city_name');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select  = parent::_getLoadSelect($field, $value, $object);
        $adapter = $this->_getReadAdapter();

        $locale       = Mage::app()->getLocale()->getLocaleCode();
        $systemLocale = Mage::app()->getDistroLocaleCode();

        $regionField = $adapter->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $adapter->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            array('lrn' => $this->_cityNameTable),
            "{$regionField} = lrn.city_id AND {$condition}",
            array());

        if ($locale != $systemLocale) {
            $nameExpr  = $adapter->getCheckSql('lrn.city_id is null', 'srn.name', 'lrn.name');
            $condition = $adapter->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                array('srn' => $this->_cityNameTable),
                "{$regionField} = srn.city_id AND {$condition}",
                array('name' => $nameExpr));
        } else {
            $select->columns(array('name'), 'lrn');
        }

        return $select;
    }

    /**
     * Load object by region id and code or default name
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $regionId
     * @param string $value
     * @param string $field
     *
     * @return Mage_Directory_Model_Resource_Region
     */
    //protected function _loadByRegion($object, $countryId, $value, $field)
    protected function _loadByRegion($object, $regionId, $value, $field)
    {
        $adapter        = $this->_getReadAdapter();
        $locale         = Mage::app()->getLocale()->getLocaleCode();
        $joinCondition  = $adapter->quoteInto('rname.city_id = city.city_id AND rname.locale = ?', $locale);
        $select         = $adapter->select()
            ->from(array('city' => $this->getMainTable()))
            ->joinLeft(
                array('rname' => $this->_cityNameTable),
                $joinCondition,
                array('name'))
            ->where('city.region_id = ?', $regionId)
            ->where("city.{$field} = ?", $value);

        $data = $adapter->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * @param Vmasciotta_CityDropdown_Model_City $city
     * @param string                             $cityName
     * @param string                              $regionId
     *
     * @return Mage_Directory_Model_Resource_Region
     */
    public function loadByName(Vmasciotta_CityDropdown_Model_City $city, $cityName, $regionId)
    {
        return $this->_loadByRegion($city, $regionId, (string)$cityName, 'default_name');
    }
}
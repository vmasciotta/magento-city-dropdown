<?php

class Vmasciotta_CityDropdown_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_factory;
    protected $_app;
    protected $_cityJson;
    protected $_regionCollection;
    protected $_cityCollection;

    /**
     * Constructor for Vmasciotta_CityDropdown_Helper_Data
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        $this->_app = !empty($args['app']) ? $args['app'] : Mage::app();
    }

    /**
     * Get Cities for specific region
     *
     * @param null $regionId
     *
     * @return $cities
     */
    protected function _getCities($regionId = null)
    {
        $cities = array();
        $cityCollection = $this->_getCityCollection();

        foreach($cityCollection as $city){
            if (!$city->getRegionId()) {
                continue;
            }

            $cities[$city->getRegionId()][$city->getId()] = array(
                'code' => $city->getId(),
                'name' => $this->__($city->getName())
            );
        }
        return $cities;
    }

    public function getCityJsonByStore($storeId = null)
    {
        if (!$this->_cityJson) {
            $store = $this->_app->getStore($storeId);
            $cacheKey = 'VMASCIOTTA_CITIES_JSON_STORE' . (string)$store->getId();

            if ($this->_app->useCache('config')) {
                $json = $this->_app->loadCache($cacheKey);
            }
            if (empty($json)) {
                $cities = $this->_getCities();
                $helper = $this->_factory->getHelper('core');
                $json = $helper->jsonEncode($cities);
            }

            if ($this->_app->useCache('config')) {
                $this->_app->saveCache($json, $cacheKey, array('config'));
            }
            $this->_cityJson = $json;
        }

        return $this->_cityJson;
    }

    public function getCityJson()
    {
        return $this->getCityJsonByStore();
    }

    protected function _getCityCollection()
    {
        if (!$this->_cityCollection) {
            $this->_cityCollection = $this->_factory->getModel('vmasciotta_citydropdown/city')->getResourceCollection();
        }
        return $this->_cityCollection;
    }
}
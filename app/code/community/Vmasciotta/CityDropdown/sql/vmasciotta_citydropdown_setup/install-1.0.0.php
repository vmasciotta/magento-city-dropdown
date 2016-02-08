<?php
/**
 * Le città non vanno basate sulla stessa nazione, ma sulle stesse province!
 * Questo perché può essere che all'interno della stessa nazione possono esistere città con lo stesso nome
 * ma in province diverse
 */


/**
 * @var $this Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

if(!$installer->getConnection()->isTableExists($installer->getTable('vmasciotta_country_region_city'))){
    /**
     * Create table 'vmasciotta/country_region_city'
     */
    $table = $installer->getConnection()
        ->newTable($installer->getTable('vmasciotta_country_region_city'))
        ->addColumn(
            'city_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Region Id'
        )
        ->addColumn(
            'region_id', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(
            'nullable' => false,
            'default'  => '0',
        ), 'Region Id in ISO-2'
        )
        ->addColumn(
            'default_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(),
            'Region Name'
        )
        ->addIndex(
            $installer->getIdxName(
                'vmasciotta_country_region_city', array('region_id')
            ),
            array('region_id')
        )
        ->setComment('Directory Country City');
    $installer->getConnection()->createTable($table);
}


/**
 * Create table 'directory/region_city_name'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('vmasciotta_country_region_city_name'))
    ->addColumn(
        'locale', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(
        'nullable' => false,
        'primary'  => true,
        'default'  => '',
    ), 'Locale'
    )
    ->addColumn(
        'city_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
        'default'  => '0',
    ), 'City Id'
    )
    ->addColumn(
        'name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
        'default'  => null,
    ), 'City Name'
    )
    ->addIndex(
        $installer->getIdxName(
            'vmasciotta_country_region_city_name', array('city_id')
        ),
        array('city_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'vmasciotta_country_region_city_name', 'city_id',
            'vmasciotta_country_region_city', 'city_id'
        ),
        'city_id', $installer->getTable('vmasciotta_country_region_city'),
        'city_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Directory Country Region City Name');
$installer->getConnection()->createTable($table);

$installer->endSetup();
<?php

namespace Aventi\CityDropDown\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_aventi_citydropdown_city = $setup->getConnection()->newTable($setup->getTable('aventi_citydropdown_city'));

        $table_aventi_citydropdown_city->addColumn(
            'city_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_aventi_citydropdown_city->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'name'
        );

        $table_aventi_citydropdown_city->addColumn(
            'postalCode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'postalCode'
        );

        $table_aventi_citydropdown_city->addColumn(
            'region_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'region_id'
        );

        $setup->getConnection()->createTable($table_aventi_citydropdown_city);
    }
}
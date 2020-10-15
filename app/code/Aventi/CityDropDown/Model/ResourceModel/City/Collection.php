<?php


namespace Aventi\CityDropDown\Model\ResourceModel\City;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aventi\CityDropDown\Model\City::class,
            \Aventi\CityDropDown\Model\ResourceModel\City::class
        );
    }
}

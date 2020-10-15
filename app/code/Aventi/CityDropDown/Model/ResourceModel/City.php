<?php


namespace Aventi\CityDropDown\Model\ResourceModel;

class City extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aventi_citydropdown_city', 'city_id');
    }
}

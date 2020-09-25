<?php


namespace Aventi\PickUpWithOffices\Model\ResourceModel\Office;

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
            \Aventi\PickUpWithOffices\Model\Office::class,
            \Aventi\PickUpWithOffices\Model\ResourceModel\Office::class
        );
    }
}

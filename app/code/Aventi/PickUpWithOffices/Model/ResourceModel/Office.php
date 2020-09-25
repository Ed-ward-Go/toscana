<?php


namespace Aventi\PickUpWithOffices\Model\ResourceModel;

class Office extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aventi_pickupwithoffices_office', 'office_id');
    }
}

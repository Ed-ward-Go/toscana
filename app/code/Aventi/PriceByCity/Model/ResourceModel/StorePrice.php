<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Model\ResourceModel;

class StorePrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aventi_pricebycity_storeprice', 'storeprice_id');
    }
}


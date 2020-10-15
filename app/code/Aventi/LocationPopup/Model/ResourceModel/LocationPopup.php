<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Model\ResourceModel;

class LocationPopup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aventi_locationpopup_locationpopup', 'locationpopup_id');
    }
}


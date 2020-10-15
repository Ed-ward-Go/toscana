<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Model\ResourceModel\LocationPopup;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'locationpopup_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aventi\LocationPopup\Model\LocationPopup::class,
            \Aventi\LocationPopup\Model\ResourceModel\LocationPopup::class
        );
    }
}


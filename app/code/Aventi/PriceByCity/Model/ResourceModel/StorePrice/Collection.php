<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Model\ResourceModel\StorePrice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'storeprice_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aventi\PriceByCity\Model\StorePrice::class,
            \Aventi\PriceByCity\Model\ResourceModel\StorePrice::class
        );
    }
}


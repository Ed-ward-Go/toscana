<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aventi\PriceByCity\Model\CityInventoryEquivalence::class,
            \Aventi\PriceByCity\Model\ResourceModel\CityInventoryEquivalence::class
        );
    }
}
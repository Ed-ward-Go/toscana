<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Api\Data;

interface CityInventoryEquivalenceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get CityInventoryEquivalence list.
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface[]
     */
    public function getItems();

    /**
     * Set product_id list.
     * @param \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

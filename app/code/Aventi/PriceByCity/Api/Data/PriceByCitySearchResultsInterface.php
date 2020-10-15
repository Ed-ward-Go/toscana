<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Api\Data;

interface PriceByCitySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PriceByCity list.
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface[]
     */
    public function getItems();

    /**
     * Set product_id list.
     * @param \Aventi\PriceByCity\Api\Data\PriceByCityInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

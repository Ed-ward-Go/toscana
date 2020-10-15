<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Api\Data;

interface StorePriceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get StorePrice list.
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface[]
     */
    public function getItems();

    /**
     * Set store list.
     * @param \Aventi\PriceByCity\Api\Data\StorePriceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}


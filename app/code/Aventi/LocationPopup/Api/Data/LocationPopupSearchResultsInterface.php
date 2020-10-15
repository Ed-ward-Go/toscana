<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Api\Data;

interface LocationPopupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get LocationPopup list.
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface[]
     */
    public function getItems();

    /**
     * Set store list.
     * @param \Aventi\LocationPopup\Api\Data\LocationPopupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}


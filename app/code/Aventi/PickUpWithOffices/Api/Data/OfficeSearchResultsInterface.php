<?php


namespace Aventi\PickUpWithOffices\Api\Data;

interface OfficeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Office list.
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param \Aventi\PickUpWithOffices\Api\Data\OfficeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

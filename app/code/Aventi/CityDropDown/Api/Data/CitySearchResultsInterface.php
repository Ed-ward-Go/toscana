<?php


namespace Aventi\CityDropDown\Api\Data;

interface CitySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get City list.
     * @return \Aventi\CityDropDown\Api\Data\CityInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Aventi\CityDropDown\Api\Data\CityInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

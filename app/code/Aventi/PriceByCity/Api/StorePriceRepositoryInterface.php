<?php
declare(strict_types=1);

namespace Aventi\PriceByCity\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StorePriceRepositoryInterface
{

    /**
     * Save StorePrice
     * @param \Aventi\PriceByCity\Api\Data\StorePriceInterface $storePrice
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\PriceByCity\Api\Data\StorePriceInterface $storePrice
    );

    /**
     * Retrieve StorePrice
     * @param string $storepriceId
     * @return \Aventi\PriceByCity\Api\Data\StorePriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($storepriceId);

    /**
     * Retrieve StorePrice matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\PriceByCity\Api\Data\StorePriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete StorePrice
     * @param \Aventi\PriceByCity\Api\Data\StorePriceInterface $storePrice
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\PriceByCity\Api\Data\StorePriceInterface $storePrice
    );

    /**
     * Delete StorePrice by ID
     * @param string $storepriceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($storepriceId);
}


<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface LocationPopupRepositoryInterface
{

    /**
     * Save LocationPopup
     * @param \Aventi\LocationPopup\Api\Data\LocationPopupInterface $locationPopup
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\LocationPopup\Api\Data\LocationPopupInterface $locationPopup
    );

    /**
     * Retrieve LocationPopup
     * @param string $locationpopupId
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($locationpopupId);

    /**
     * Retrieve LocationPopup matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete LocationPopup
     * @param \Aventi\LocationPopup\Api\Data\LocationPopupInterface $locationPopup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\LocationPopup\Api\Data\LocationPopupInterface $locationPopup
    );

    /**
     * Delete LocationPopup by ID
     * @param string $locationpopupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($locationpopupId);
}


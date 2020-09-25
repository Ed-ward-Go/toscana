<?php


namespace Aventi\PickUpWithOffices\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface OfficeRepositoryInterface
{

    /**
     * Save Office
     * @param \Aventi\PickUpWithOffices\Api\Data\OfficeInterface $office
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Aventi\PickUpWithOffices\Api\Data\OfficeInterface $office
    );

    /**
     * Retrieve Office
     * @param string $officeId
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($officeId);

    /**
     * Retrieve Office matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Office
     * @param \Aventi\PickUpWithOffices\Api\Data\OfficeInterface $office
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Aventi\PickUpWithOffices\Api\Data\OfficeInterface $office
    );

    /**
     * Delete Office by ID
     * @param string $officeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($officeId);
}

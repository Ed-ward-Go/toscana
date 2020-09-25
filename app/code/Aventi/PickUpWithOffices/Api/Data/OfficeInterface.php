<?php


namespace Aventi\PickUpWithOffices\Api\Data;

interface OfficeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const OFFICE_ID = 'office_id';
    const ADDRESS = 'address';
    const TITLE = 'title';
    const CITY = 'city';
    const DESCRIPTION = 'description';
    const SCHEDULE = 'schedule';
    const SAP = 'sap';

    /**
     * Get office_id
     * @return string|null
     */
    public function getOfficeId();

    /**
     * Set office_id
     * @param string $officeId
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setOfficeId($officeId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setTitle($title);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\PickUpWithOffices\Api\Data\OfficeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PickUpWithOffices\Api\Data\OfficeExtensionInterface $extensionAttributes
    );

    /**
     * Get address
     * @return string|null
     */
    public function getAddress();

    /**
     * Set address
     * @param string $address
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setAddress($address);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setCity($city);

    /**
     * Get schedule
     * @return string|null
     */
    public function getSchedule();

    /**
     * Set schedule
     * @param string $schedule
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setSchedule($schedule);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setDescription($description);

    /**
     * Get sap
     * @return string|null
     */
    public function getSap();

    /**
     * Set sap
     * @param string $sap
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setSap($sap);
}

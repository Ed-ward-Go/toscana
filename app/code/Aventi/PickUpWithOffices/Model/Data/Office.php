<?php


namespace Aventi\PickUpWithOffices\Model\Data;

use Aventi\PickUpWithOffices\Api\Data\OfficeInterface;

class Office extends \Magento\Framework\Api\AbstractExtensibleObject implements OfficeInterface
{

    /**
     * Get office_id
     * @return string|null
     */
    public function getOfficeId()
    {
        return $this->_get(self::OFFICE_ID);
    }

    /**
     * Set office_id
     * @param string $officeId
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setOfficeId($officeId)
    {
        return $this->setData(self::OFFICE_ID, $officeId);
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\PickUpWithOffices\Api\Data\OfficeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PickUpWithOffices\Api\Data\OfficeExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get address
     * @return string|null
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
    }

    /**
     * Set address
     * @param string $address
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * Get city
     * @return string|null
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get schedule
     * @return string|null
     */
    public function getSchedule()
    {
        return $this->_get(self::SCHEDULE);
    }

    /**
     * Set schedule
     * @param string $schedule
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setSchedule($schedule)
    {
        return $this->setData(self::SCHEDULE, $schedule);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get sap
     * @return string|null
     */
    public function getSap()
    {
        return $this->_get(self::SAP);
    }

    /**
     * Set sap
     * @param string $sap
     * @return \Aventi\PickUpWithOffices\Api\Data\OfficeInterface
     */
    public function setSap($sap)
    {
        return $this->setData(self::SAP, $sap);
    }


}

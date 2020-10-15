<?php
declare(strict_types=1);

namespace Aventi\LocationPopup\Model\Data;

use Aventi\LocationPopup\Api\Data\LocationPopupInterface;

class LocationPopup extends \Magento\Framework\Api\AbstractExtensibleObject implements LocationPopupInterface
{

    /**
     * Get locationpopup_id
     * @return string|null
     */
    public function getLocationpopupId()
    {
        return $this->_get(self::LOCATIONPOPUP_ID);
    }

    /**
     * Set locationpopup_id
     * @param string $locationpopupId
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     */
    public function setLocationpopupId($locationpopupId)
    {
        return $this->setData(self::LOCATIONPOPUP_ID, $locationpopupId);
    }

    /**
     * Get store
     * @return string|null
     */
    public function getStore()
    {
        return $this->_get(self::STORE);
    }

    /**
     * Set store
     * @param string $store
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     */
    public function setStore($store)
    {
        return $this->setData(self::STORE, $store);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\LocationPopup\Api\Data\LocationPopupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\LocationPopup\Api\Data\LocationPopupExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
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
     * @return \Aventi\LocationPopup\Api\Data\LocationPopupInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }
}


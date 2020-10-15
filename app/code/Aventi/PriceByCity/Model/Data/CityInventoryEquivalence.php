<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Model\Data;

use Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface;

class CityInventoryEquivalence extends \Magento\Framework\Api\AbstractExtensibleObject implements CityInventoryEquivalenceInterface
{

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entity_id
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId()
    {
        return $this->_get(self::REGION_ID);
    }

    /**
     * Set region_id
     * @param string $regionId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Get source_id
     * @return string|null
     */
    public function getSourceId()
    {
        return $this->_get(self::SOURCE_ID);
    }

    /**
     * Set source_id
     * @param string $sourceId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setSourceId($sourceId)
    {
        return $this->setData(self::SOURCE_ID, $sourceId);
    }
}

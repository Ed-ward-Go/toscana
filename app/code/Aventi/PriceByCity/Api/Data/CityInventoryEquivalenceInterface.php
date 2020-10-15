<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Api\Data;

interface CityInventoryEquivalenceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ENTITY_ID = 'entity_id';
    const REGION_ID = 'region_id';
    const PRODUCT_ID = 'product_id';
    const SOURCE_ID = 'source_id';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setEntityId($entityId);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setProductId($productId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceExtensionInterface $extensionAttributes
    );

    /**
     * Get region_id
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set region_id
     * @param string $regionId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setRegionId($regionId);

    /**
     * Get source_id
     * @return string|null
     */
    public function getSourceId();

    /**
     * Set source_id
     * @param string $sourceId
     * @return \Aventi\PriceByCity\Api\Data\CityInventoryEquivalenceInterface
     */
    public function setSourceId($sourceId);
}

<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\PriceByCity\Api\Data;

interface PriceByCityInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PRICE = 'price';
    const STOREPRICE_ID = 'storeprice_id';
    const SOURCE_CODE = 'source_code';
    const PRODUCT_ID = 'product_id';

    /**
     * Get storeprice_id
     * @return int|null
     */
    public function getStorepriceId();

    /**
     * Set storeprice_id
     * @param int|null $storepriceId
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setStorepriceId($storepriceId);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setProductId($productId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Aventi\PriceByCity\Api\Data\PriceByCityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aventi\PriceByCity\Api\Data\PriceByCityExtensionInterface $extensionAttributes
    );

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setPrice($price);

    /**
     * Get source_code
     * @return string|null
     */
    public function getSourceCode();

    /**
     * Set source_code
     * @param string $sourceCode
     * @return \Aventi\PriceByCity\Api\Data\PriceByCityInterface
     */
    public function setSourceCode($sourceCode);
}

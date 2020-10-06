<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\Credit\Api\Data;

interface CreditSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Credit list.
     * @return \Aventi\Credit\Api\Data\CreditInterface[]
     */
    public function getItems();

    /**
     * Set available list.
     * @param \Aventi\Credit\Api\Data\CreditInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}


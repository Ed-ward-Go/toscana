<?php

namespace Aventi\SAP\Observer\Catalog\Update\After;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Sync implements ObserverInterface
{
    /**
     * @var \MGS\Brand\Observer\CatalogProductSaveAfterObserver
     */
    private $catalogProductSaveAfterObserver;

    public function __construct(
        \MGS\Brand\Observer\CatalogProductSaveAfterObserver $catalogProductSaveAfterObserver
    ) {
        $this->catalogProductSaveAfterObserver = $catalogProductSaveAfterObserver;
    }

    public function execute(EventObserver $observer)
    {
        $this->catalogProductSaveAfterObserver->execute($observer);
    }
}

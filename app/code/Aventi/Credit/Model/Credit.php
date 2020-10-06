<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\Credit\Model;

use Aventi\Credit\Api\Data\CreditInterface;
use Aventi\Credit\Api\Data\CreditInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Credit extends \Magento\Framework\Model\AbstractModel
{
    protected $creditDataFactory;

    protected $_eventPrefix = 'aventi_credit_credit';
    protected $dataObjectHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CreditInterfaceFactory $creditDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Aventi\Credit\Model\ResourceModel\Credit $resource
     * @param \Aventi\Credit\Model\ResourceModel\Credit\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CreditInterfaceFactory $creditDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Aventi\Credit\Model\ResourceModel\Credit $resource,
        \Aventi\Credit\Model\ResourceModel\Credit\Collection $resourceCollection,
        array $data = []
    ) {
        $this->creditDataFactory = $creditDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve credit model with credit data
     * @return CreditInterface
     */
    public function getDataModel()
    {
        $creditData = $this->getData();

        $creditDataObject = $this->creditDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $creditDataObject,
            $creditData,
            CreditInterface::class
        );

        return $creditDataObject;
    }
}

<?php

namespace Aventi\PickUpWithOffices\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $coreSession;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        parent::__construct($context);
        $this->coreSession = $coreSession;
    }

    /**
     * @param $incremendId
     */
    public function setValue($officeId)
    {
        $this->coreSession->start();
        $this->coreSession->setPickUp($officeId);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $this->coreSession->start();
        return $this->coreSession->getPickUp();
    }

    /**
     * @return mixed
     */
    public function unSetValue()
    {
        $this->coreSession->start();
        return $this->coreSession->unsgetPickUp();
    }

}



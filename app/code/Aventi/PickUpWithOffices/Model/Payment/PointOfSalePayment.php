<?php
declare(strict_types=1);

namespace Aventi\PickUpWithOffices\Model\Payment;

class PointOfSalePayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "pointofsalepayment";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}


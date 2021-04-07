<?php
namespace Aventi\ForceLogin\Block;

use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection;
use Magento\Framework\View\Element\Template;

class TermsAndConditions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Collection
     */
    private $argeementsCollection;

    public function __construct(
        Template\Context $context,
        array $data = [],
        \Magento\CheckoutAgreements\Api\Data\AgreementInterfaceFactory $argeementsCollection
    ) {
        parent::__construct($context, $data);
        $this->argeementsCollection = $argeementsCollection;
    }

    public function getAgreements()
    {
        return $this->argeementsCollection->create()->getCollection();
    }
}

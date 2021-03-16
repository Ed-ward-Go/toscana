<?php
namespace Aventi\PriceByCity\Plugin\CatalogSearch\Model\Search;

use Magento\Framework\Search\RequestInterface;
use Psr\Log\LoggerInterface;

class IndexBuilder
{
    const PRICE_BY_CITY_TABLE = 'aventi_pricebycity_storeprice';

    /**
     * @var LoggerInterface
     */
    private $_logger;
    /**
     * @var \Aventi\LocationPopup\Helper\Data
     */
    private $_locationHelper;

    public function __construct(
        LoggerInterface $logger,
        \Aventi\LocationPopup\Helper\Data $locationHelper
    ) {
        $this->_logger = $logger;
        $this->_locationHelper = $locationHelper;
    }

    public function aroundBuild(\Magento\CatalogSearch\Model\Search\IndexBuilder $subject, callable $proceed, RequestInterface $request)
    {
        $select = $proceed($request);
        $source = $this->_locationHelper->getValue();
        $joinConditions = 'search_index.entity_id = pricebycity.product_id AND pricebycity.source_code = "' . $source['id'] . '"';
        $select->join(['pricebycity' => self::PRICE_BY_CITY_TABLE], $joinConditions, [])->where('pricebycity.source_code = "' . $source['id'] . '"');

        return $select;
    }

}

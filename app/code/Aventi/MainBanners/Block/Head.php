<?php


namespace Aventi\MainBanners\Block;

use Magento\Framework\View\Element\Template;

class Head extends Template
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepository;
 
    /**
     * Header constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->assetRepository = $context->getAssetRepository();
    }
 
    /**
     * @return string
     */
    public function getCustomCSS()
    {
        $asset_repository = $this->assetRepository;
        $asset  = $asset_repository->createAsset('Aventi_MainBanners::css/ban.css');
        $url    = $asset->getUrl();
         
        return $url;
    }
}
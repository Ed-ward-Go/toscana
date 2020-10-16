<?php

namespace Aventi\LocationPopup\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RegionList implements OptionSourceInterface
{
    protected $country;

    public function __construct(
        \Magento\Directory\Model\Country $country
    )
    {
        $this->country = $country;
    }

    public function toOptionArray()
    {
        $arr = $this->_toArray("EC");
        $ret = [];
        $i = 0;
        foreach ($arr as $key => $value)
        {
            if($i == 0){
                $ret[] = [
                    'value' => '',
                    'label' => '-- Please select a region --'
                ];
            }else{
                $ret[] = [
                    'value' => $value['value'],
                    'label' => $value['title']
                ];
            }
            $i++;
        }

        return $ret;
    }

    private function _toArray($code)
    {
        $regionCollection = $this->country->loadByCode($code)->getRegions();
        $regions = $regionCollection->loadData()->toOptionArray(true);
        return $regions;
    }

}

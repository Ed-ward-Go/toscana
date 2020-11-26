<?php

namespace Aventi\SAP\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class SAP extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * get postal code of table temporal
     *
     * @param $region
     * @param $city
     * @method
     * date 20/06/19/09:07 AM
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return int|string|null
     */
    public function getPostalCode($region, $city)
    {
        $connection = $this->resourceConnection->getConnection();
        $postalCode = null;
        if(!is_null($region)){

            $sql = 'SELECT postalCode from  aventi_citydropdown_city acc inner join directory_country_region dcr on dcr.region_id = acc.region_id
                    where dcr.default_name like "%' . $region . '%"  and acc.name like "%' . $city . '%"';
            $postalCode = $connection->fetchOne($sql);
        }else{
            $sql = 'SELECT postalCode from  aventi_citydropdown_city acc
                    where acc.name like "%' . $city . '%"';
            $postalCode = $connection->fetchOne($sql);
        }
        return (is_numeric($postalCode)) ? $postalCode : null;
    }

    /**
     *
     * Get the customer id
     *
     * @param $cn
     * @method
     * date 20/06/19/11:03 AM
     * @author Carlos Hernan Aguilar Hurtado <caguilar@aventi.co>
     * @return int|string|null
     */
    public function getCustomerId($cn)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT entity_id from customer_entity_varchar where `value` = "' . $cn . '"';
        $id = $connection->fetchOne($sql);
        return (is_numeric($id)) ? $id : null;
    }

    public function managerCustomerAddressSAP($address, $customerId = null, $addressId = null)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT entity_id from customer_address_entity where `sap` = "' . addslashes($address) . '" AND `parent_id` = "' . addslashes($customerId) . '"';
        $id = $connection->fetchOne($sql);
        if (is_numeric($id)) {
            return $id;
        } elseif (is_numeric($addressId)) {
            $sql = 'UPDATE customer_address_entity  SET `sap` = "' . addslashes($address) . '" WHERE  entity_id = ' . (int)$addressId;
            $connection->query($sql);
            return $addressId;
        }
        return null;
    }

    public function managerCustomerAddressSAPForce($address, $addressId = null)
    {
        $connection = $this->resourceConnection->getConnection();
        if (is_numeric($addressId)) {
            $sql = 'UPDATE customer_address_entity  SET `sap` = "' . addslashes($address) . '" WHERE  entity_id = ' . (int)$addressId;
            $connection->query($sql);
            return $addressId;
        }
        return null;
    }

    public function getCategory($sap)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT entity_id from catalog_category_entity_varchar where `value` = "' . trim($sap) . '" and  attribute_id = 174 ';
        $id = $connection->fetchOne($sql);
        return (is_numeric($id)) ? $id : null;
    }

    public function getCategoryByName($GranFamilia, $familia, $subFamilia)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = <<<SQL
        SELECT su.entity_id as 'Clase', fa.entity_id as 'Tipo', gran.entity_id as 'Grupo'
        FROM catalog_category_entity su
        INNER JOIN  catalog_category_entity fa on (fa.entity_id = su.parent_id)
        INNER JOIN  catalog_category_entity gran on (gran.entity_id = fa.parent_id)
        INNER JOIN catalog_category_entity_varchar sun on (sun.entity_id = su.entity_id and sun.attribute_id = 174 and sun.value = '__SUBFAMILIA__')
        INNER JOIN catalog_category_entity_varchar fan on (fan.entity_id = fa.entity_id and fan.attribute_id = 174 and fan.value = '__FAMILIA__')
        INNER JOIN catalog_category_entity_varchar grann on (grann.entity_id = gran.entity_id and grann.attribute_id = 174 and grann.value = '__GRANFAMILIA__')
SQL;

        $sql = str_replace(['__SUBFAMILIA__', '__FAMILIA__', '__GRANFAMILIA__'], [$subFamilia, $familia, $GranFamilia], $sql);

        $ids = $connection->fetchRow($sql);
        // return $ids;
        return (is_array($ids)) ? $ids : null;
    }

    public function getCategoryParent($sap)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = '
                SELECT c.path,v.entity_id
                FROM `catalog_category_entity_varchar` v
                INNER JOIN catalog_category_entity c on (c.entity_id = v.entity_id)
                WHERE v.`attribute_id` = 180 and v.value =  "' . $sap . '"
                        ';
        return $connection->fetchRow($sql);
    }

    public function getAddressSAP($addressId)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT sap from customer_address_entity where `entity_id` = "' . $addressId . '"';
        $id = $connection->fetchOne($sql);
        if ($id != null) {
            return $id;
        }
        return null;
    }

    /**
     *  Get in SKU  in base to itemCode
     * @param $sapId
     * @return string|null
     */
    public function getSkuBySAP($sapId)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql=<<<SQL
             SELECT sku
             FROM `eav_attribute` e
             INNER JOIN catalog_product_entity_varchar v on (e.attribute_id = v.attribute_id )
             INNER JOIN catalog_product_entity p on (p.entity_id = v.entity_id )
             where e.attribute_code = 'sap'
             and e.entity_type_id = '4'
             and v.value = 'ID'
             and v.store_id = 1
SQL;
        $id = $connection->fetchOne(str_replace('ID', $sapId, $sql));
        if ($id != null) {
            return $id;
        }
        return null;
    }

    public function getCatalogBySku($sapId)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql=<<<SQL
             SELECT v.value
             FROM `eav_attribute` e
             INNER JOIN catalog_product_entity_varchar v on (e.attribute_id = v.attribute_id )
             INNER JOIN catalog_product_entity p on (p.entity_id = v.entity_id )
             where e.attribute_code = 'sap'
             and e.entity_type_id = '4'
             and (p.sku = 'ID' or  v.value = 'ID')
             and v.store_id = 1
SQL;
        $id = $connection->fetchOne(str_replace('ID', $sapId, $sql));
        if ($id != null) {
            return $id;
        }
        return null;
    }

    public function managerCompanySAP($sap, $companyId = null)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT id from aw_ca_company where `sap` = "' . addslashes($sap) . '"';
        $id = $connection->fetchOne($sql);
        if (is_numeric($id)) {
            return $id;
        } elseif (is_numeric($companyId)) {
            $sql = 'UPDATE aw_ca_company  SET `sap` = "' . addslashes($sap) . '" WHERE  id = ' . (int)$companyId;
            $connection->query($sql);
            return $companyId;
        }
        return null;
    }

    public function getPostalCodeByCity($name)
    {

        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT postalCode from aventi_citydropdown_city where `name` = "' . addslashes($name) . '"';
        $id = $connection->fetchOne($sql);
        return (is_numeric($id)) ? $id : null ;
    }

    public function getRegionByCity($city)
    {

        $connection = $this->resourceConnection->getConnection();
        $sql = 'SELECT region_id from aventi_citydropdown_city where `name` LIKE "%' . addslashes($city) . '%"';
        $id = $connection->fetchOne($sql);
        return (is_numeric($id)) ? (int)$id : null ;
    }

    public function getLastCategory($category)
    {
        $connection = $this->resourceConnection->getConnection();
        $sql = <<<SQL
        SELECT su.entity_id
        FROM catalog_category_entity su
        INNER JOIN catalog_category_entity_varchar sun on (sun.entity_id = su.entity_id and sun.attribute_id = 173) WHERE sun.value = '__CATEGORY__' GROUP BY su.entity_id
SQL;

        $sql = str_replace(['__CATEGORY__'], [$category], $sql);

        $id = $connection->fetchOne($sql);
        return (is_numeric($id)) ? $id : null;
    }

}

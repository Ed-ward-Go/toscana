<?php

namespace Aventi\SAP\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;
    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        CustomerSetupFactory $customerSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerAddressEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
            $attributeSetId = $customerAddressEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $customerSetup->addAttribute('customer_address', 'identification_customer', [
                'type'          => 'varchar',
                'label'         => 'Número de identificación',
                'input'         => 'text',
                'required'      =>  false,
                'visible'       =>  true,
                'user_defined'  =>  true,
                'sort_order'    =>  30,
                'position'      =>  30,
                'system'        =>  0,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'identification_customer')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]);
            $attribute->save();

            /*$customerSetup->addAttribute('customer_address', 'serie', [
                'type'          => 'varchar',
                'label'         => 'Serie',
                'input'         => 'text',
                'required'      =>  false,
                'visible'       =>  true,
                'user_defined'  =>  true,
                'sort_order'    =>  30,
                'position'      =>  30,
                'system'        =>  0,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'serie')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]);
            $attribute->save();

            $customerSetup->addAttribute('customer_address', 'warehouse_group', [
                'type'          => 'varchar',
                'label'         => 'Grupo bodega',
                'input'         => 'text',
                'required'      =>  false,
                'visible'       =>  true,
                'user_defined'  =>  true,
                'sort_order'    =>  30,
                'position'      =>  30,
                'system'        =>  0,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'warehouse_group')
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]);
            $attribute->save();*/
        }

        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute(
                'order_address',
                'identification_customer',
                [
                    'type' => 'varchar',
                    'length' => 30,
                    'visible' => false,
                    'required' => false,
                    'grid' => false
                ]
            );

            /*$salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute('order_address', 'serie',
                [
                    'type' => 'varchar',
                    'length' => 30,
                    'visible' => false,
                    'required' => false,
                    'grid' => false
                ]
            );

            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute('order_address', 'warehouse_group',
                [
                    'type' => 'varchar',
                    'length' => 30,
                    'visible' => false,
                    'required' => false,
                    'grid' => false
                ]
            );*/
        }

        if (version_compare($context->getVersion(), "1.0.3", "<")) {
            $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
            $quoteSetup->addAttribute(
                'quote_address',
                'identification_customer',
                [
                    'type' => 'varchar',
                    'length' => 30,
                    'visible' => false,
                    'required' => false,
                    'grid' => false
                ]
            );
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'slp_code', [
                'type' => 'varchar',
                'label' => 'slp_code',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => false,
                'position' => 333,
                'system' => false,
                'backend' => ''
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'slp_code')
                ->addData(['used_in_forms' => [
                    'adminhtml_customer',
                    'adminhtml_checkout'
                ]
                ]);
            $attribute->save();

            /*$quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
            $quoteSetup->addAttribute('quote_address', 'serie',
                [
                    'type' => 'varchar',
                    'length' => 30,
                    'visible' => false,
                    'required' => false,
                    'grid' => false
                ]
            );

            $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
            $quoteSetup->addAttribute('quote_address', 'warehouse_group',
                [
                    'type' => 'varchar',
                    'length' => 30,
                    'visible' => false,
                    'required' => false,
                    'grid' => false
                ]
            );*/

        }

        if (version_compare($context->getVersion(), "1.0.4", "<")) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'sap_customer_id', [
                'type' => 'varchar',
                'label' => 'sap_customer_id',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => false,
                'position' => 333,
                'system' => false,
                'backend' => ''
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'sap_customer_id')
                ->addData(['used_in_forms' => [
                    'adminhtml_customer',
                    'adminhtml_checkout'
                ]
                ]);
            $attribute->save();

            /*$customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'owner_code', [
                'type' => 'varchar',
                'label' => 'owner_code',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => false,
                'position' => 333,
                'system' => false,
                'backend' => ''
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'owner_code')
                ->addData(['used_in_forms' => [
                    'adminhtml_customer',
                    'adminhtml_checkout'
                ]
                ]);
            $attribute->save();

            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'user_code', [
              'type' => 'varchar',
              'label' => 'user_code',
              'input' => 'text',
              'source' => '',
              'required' => false,
              'visible' => false,
              'position' => 333,
              'system' => false,
              'backend' => ''
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'user_code')
              ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout'
              ]
              ]);
            $attribute->save();*/
        }
    }
}

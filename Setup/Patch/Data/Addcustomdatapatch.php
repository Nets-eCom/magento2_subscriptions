<?php

namespace Dibs\EasyCheckout\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class Addcustomdatapatch implements DataPatchInterface {

    private $_moduleDataSetup;
    private $_eavSetupFactory;

    public function __construct(
            ModuleDataSetupInterface $moduleDataSetup,
            CategorySetupFactory $categorySetupFactory,
            AttributeSetFactory $attributeSetFactory,
            EavSetup $eavSetup,
            EavSetupFactory $eavSetupFactory
    ) {
        $this->_moduleDataSetup = $moduleDataSetup;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetup = $eavSetup;
    }

    public function apply() {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

		    $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'enable_subscription');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'enable_subscription', [
            'group' => 'Nets Subscription',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Enable Subscription',
            'input' => 'boolean',
            'class' => 'enable_subscription',
            'source' => \Magento\Catalog\Model\Product\Attribute\Source\Boolean::class,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => false,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false
        ]);

        /*$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'add_signup_fee');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'add_signup_fee', [
            'group' => 'Nets Subscription',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Add Sign-up Fee',
            'input' => 'boolean',
            //'frontend_class' => 'required-entry',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'unique' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true
        ]);*/

		    $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'signup_fee');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'signup_fee', [
            'group' => 'Nets Subscription',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Sign-up Fee',
            'input' => 'text',
            //'frontend_class' => 'required-entry',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'unique' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true
        ]);

	    	$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'signup_fee_name');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'signup_fee_name', [
            'group' => 'Nets Subscription',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Sign-up Fee Name',
            'input' => 'text',
            //'frontend_class' => 'required-entry',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'unique' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true
        ]);

		    /*$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'subscription_end_date');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'subscription_end_date', [
            'group' => 'Nets Subscription',
            'type' => 'datetime',
            'backend' => '',
            'frontend' => '',
            'label' => 'Subscription End Date',
            'input' => 'date',
            'readonly' => 'readonly',
            //'frontend_class' => 'required-entry',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'unique' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true
        ]);*/

		    /*$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'display_time_slots_to_customer');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'display_time_slots_to_customer', [
            'group' => 'Nets Subscription',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Display Interval in Frontend',
            'input' => 'boolean',
            //'frontend_class' => 'required-entry',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'unique' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true
        ]);*/

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'nets_interval');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'nets_interval', [
            'group' => 'Nets Subscription',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Frequency',
            'input' => 'select',
            'class' => '',
            'source' => 'Dibs\EasyCheckout\Model\Source\Frequency',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'apply_to' => ''
        ]);

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'nets_sub_interval');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'nets_sub_interval', [
            'group' => 'Nets Subscription',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Period',
            'input' => 'select',
            'class' => '',
            'source' => 'Dibs\EasyCheckout\Model\Source\Period',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'apply_to' => ''
        ]);

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'nets_sub_interval_time');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'nets_sub_interval_time', [
            'group' => 'Nets Subscription',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Subscription Expires After',
            'input' => 'select',
            'class' => '',
            'source' => 'Dibs\EasyCheckout\Model\Source\SubscriptionExpire',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'apply_to' => ''
        ]);
        
        /*$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'subscription_expires_after');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'subscription_expires_after', [
            'group' => 'Nets Subscription',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Subscription Expires After',
            'input' => 'text',
            //'frontend_class' => 'required-entry',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'unique' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true
        ]);*/
        
        /** @var EavSetup $eavSetup */
        try {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $this->_moduleDataSetup]);
            $attributeSet = $this->attributeSetFactory->create();
            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

            //load all attribute set ids
			      $entityTypeId = $this->eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetIds = $this->eavSetup->getAllAttributeSetIds($entityTypeId);

            //$newAttributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'Default'); //load specific attribute set ids

            $attributeGroupName = 'Nets Subscription';
            foreach ($attributeSetIds as $attributeSetId) {
                if ($attributeSetId) {
                    $categorySetup->addAttributeGroup(
                            $entityTypeId,
                            $attributeSetId,
                            $attributeGroupName,
                            800 // sort order
                    );
                    $attributeGroupId1 = $categorySetup->getAttributeGroupId(
                            $entityTypeId,
                            $attributeSetId,
                            $attributeGroupName
                    );
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getDependencies() {
        return [];
    }

    public function getAliases() {
        return [];
    }

    public static function getVersion() {
        return '1.0.2';
    }

}
 
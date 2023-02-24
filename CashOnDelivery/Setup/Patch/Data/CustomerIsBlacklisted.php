<?php

namespace Codilar\CashOnDelivery\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CustomerIsBlacklisted implements DataPatchInterface
{
    /**
     * @var Config
     */
    private $eavConfig;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * VendorName constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    protected function createCustomerAttribute($name, $definition)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $name,
            $definition
        );
        $customAttribute = $this->eavConfig->getAttribute('customer', $name);
        $customAttribute->setData(
            'used_in_forms',
            [
                'customer_account_create',
                'customer_address_edit',
                'customer_register_address',
                'customer_account_edit',
                'adminhtml_customer'
            ]
        );
        $customAttribute->save();
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->createCustomerAttribute('is_blacklisted', [
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Is Blacklisted',
            'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'input' => 'boolean',
            'class' => '',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '0',
            'sort_order' => 250,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'unique' => false,
            'system'=> false
        ]);
    }
}

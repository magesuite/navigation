<?php

namespace MageSuite\Navigation\Setup\Patch\Data;

class AddUrlDisabledAttribute implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    public function __construct(
        protected \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        protected \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
    ) {}

    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();
        $eavSetup = $this->eavSetupFactory->create();

        if ($eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'url_disabled')) {
            return $this;
        }

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'url_disabled',
            [
                'type' => 'int',
                'label' => 'Disable URL',
                'input' => 'boolean',
                'default' => '0',
                'visible' => true,
                'required' => false,
                'sort_order' => 40,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General Information'
            ]
        );

        $connection->endSetup();
        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}

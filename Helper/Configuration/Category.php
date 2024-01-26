<?php

declare(strict_types=1);

namespace MageSuite\Navigation\Helper\Configuration;

class Category
{
    public const XML_PATH_NAVIGATION_CATEGORY_IMAGE_TEASER_MAX_LEVEL = 'navigation/category/image_teaser_max_level';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getMaxCategoryLevelForImageTeaser(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_NAVIGATION_CATEGORY_IMAGE_TEASER_MAX_LEVEL);
    }
}

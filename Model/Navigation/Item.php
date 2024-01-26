<?php

namespace MageSuite\Navigation\Model\Navigation;

class Item extends \Magento\Framework\DataObject
{
    protected \MageSuite\CategoryIcon\Helper\CategoryIcon $categoryIconHelper;
    protected \MageSuite\Category\Helper\Category $categoryHelper;
    protected \MageSuite\Navigation\Helper\Configuration\Category $categoryConfig;
    protected \MageSuite\Navigation\Model\Navigation\FeaturedProductsFactory $featuredProductsFactory;
    protected \MageSuite\Navigation\Model\Navigation\ImageTeaserFactory $imageTeaserFactory;
    protected \MageSuite\Navigation\Service\Category\CustomUrlGenerator $customUrlGenerator;
    protected \Magento\Catalog\Api\Data\CategoryInterface $category;

    protected \MageSuite\Navigation\Model\Navigation\FeaturedProducts $featuredProducts;
    protected \MageSuite\Navigation\Model\Navigation\ImageTeaser $imageTeaser;

    public function __construct(
        \MageSuite\CategoryIcon\Helper\CategoryIcon $categoryIconHelper,
        \MageSuite\Category\Helper\Category $categoryHelper,
        \MageSuite\Navigation\Helper\Configuration\Category $categoryConfig,
        \MageSuite\Navigation\Model\Navigation\FeaturedProductsFactory $featuredProductsFactory,
        \MageSuite\Navigation\Model\Navigation\ImageTeaserFactory $imageTeaserFactory,
        \MageSuite\Navigation\Service\Category\CustomUrlGenerator $customUrlGenerator,
        \Magento\Catalog\Api\Data\CategoryInterface $category,
        array $data = []
    ) {
        parent::__construct($data);

        $this->category = $category;
        $this->featuredProductsFactory = $featuredProductsFactory;
        $this->imageTeaserFactory = $imageTeaserFactory;
        $this->customUrlGenerator = $customUrlGenerator;
        $this->categoryHelper = $categoryHelper;
        $this->categoryIconHelper = $categoryIconHelper;
        $this->categoryConfig = $categoryConfig;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->category->getId();
    }

    /**
     * @param $label string
     */
    public function setLabel($label)
    {
        $this->setData('label', $label);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if ($this->hasData('label')) {
            return $this->getData('label');
        }

        return $this->category->getName();
    }

    /**
     * @param $identifier string
     */
    public function setIdentifier($identifier)
    {
        $this->setData('identifier', $identifier);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        if ($this->hasData('identifier')) {
            return $this->getData('identifier');
        }

        return $this->category->getCategoryIdentifier();
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->category->getParentId();
    }

    public function getUrl(): string
    {
        if ($this->isUrlDisabled()) {
            return '#';
        }

        $customUrl = $this->category->getCategoryCustomUrl();

        if (!$customUrl) {
            return $this->category->getUrl();
        }

        return $this->customUrlGenerator->generate($customUrl);
    }

    public function getProductCount(): int
    {
        return $this->categoryHelper->getProductCount($this->category);
    }

    public function getFeaturedProducts(): \MageSuite\Navigation\Model\Navigation\FeaturedProducts
    {
        if (!isset($this->featuredProducts)) {
            $this->featuredProducts = $this->featuredProductsFactory->create(['category' => $this->category]);
        }

        return $this->featuredProducts;
    }

    public function getImageTeaser(): \MageSuite\Navigation\Model\Navigation\ImageTeaser
    {
        if (!isset($this->imageTeaser)) {
            $this->imageTeaser = $this->imageTeaserFactory->create(['category' => $this->category]);
        }

        return $this->imageTeaser;
    }

    public function hasFeaturedProducts(): bool
    {
        return !empty($this->getFeaturedProducts()->getProducts());
    }

    public function hasSubItems(): bool
    {
        return !empty($this->getSubItems());
    }

    public function hasImageTeaser(): bool
    {
        if ($this->category->getLevel() > $this->categoryConfig->getMaxCategoryLevelForImageTeaser()) {
            return false;
        }

        return !empty($this->getImageTeaser()->getSlides());
    }

    public function hasCustomUrl(): bool
    {
        return !empty($this->category->getCategoryCustomUrl());
    }

    public function getCustomUrl(): ?string
    {
        return $this->category->getCategoryCustomUrl();
    }

    public function isUrlDisabled(): bool
    {
        return (bool)$this->category->getUrlDisabled();
    }

    public function getIdentities(): array
    {
        return $this->category->getIdentities();
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryIcon(): ?string
    {
        return $this->categoryIconHelper->getUrl($this->category);
    }

    /**
     * @return false|string|null
     */
    public function getCategoryIconMimeType()
    {
        return $this->categoryIconHelper->getMimeType($this->category);
    }

    public function getCategory(): \Magento\Catalog\Api\Data\CategoryInterface
    {
        return $this->category;
    }
}

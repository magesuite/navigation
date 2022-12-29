<?php

namespace MageSuite\Navigation\Model\Navigation;

class Item extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var FeaturedProductsFactory
     */
    protected $featuredProductsFactory;

    /**
     * @var null|FeaturedProducts
     */
    protected $featuredProducts = null;

    /**
     * @var ImageTeaserFactory
     */
    protected $imageTeaserFactory;

    /**
     * @var null|ImageTeaser
     */
    protected $imageTeaser = null;

    /**
     * @var \MageSuite\Navigation\Service\Category\CustomUrlGenerator
     */
    protected $customUrlGenerator;

    /**
     * @var \MageSuite\Category\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var \MageSuite\CategoryIcon\Helper\CategoryIcon
     */
    protected $categoryIconHelper;

    public function __construct(
        \Magento\Catalog\Api\Data\CategoryInterface $category,
        FeaturedProductsFactory $featuredProductsFactory,
        ImageTeaserFactory $imageTeaserFactory,
        \MageSuite\Navigation\Service\Category\CustomUrlGenerator $customUrlGenerator,
        \MageSuite\Category\Helper\Category $categoryHelper,
        \MageSuite\CategoryIcon\Helper\CategoryIcon $categoryIconHelper,
        array $data = []
    ) {
        parent::__construct($data);

        $this->category = $category;
        $this->featuredProductsFactory = $featuredProductsFactory;
        $this->imageTeaserFactory = $imageTeaserFactory;
        $this->customUrlGenerator = $customUrlGenerator;
        $this->categoryHelper = $categoryHelper;
        $this->categoryIconHelper = $categoryIconHelper;
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

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl()
    {
        $customUrl = $this->isUrlDisabled() ? '#' : $this->category->getCategoryCustomUrl();

        if (!$customUrl) {
            return $this->category->getUrl();
        }

        return $this->customUrlGenerator->generate($customUrl);
    }

    /**
     * @return int
     */
    public function getProductCount()
    {
        return $this->categoryHelper->getProductCount($this->category);
    }

    /**
     * @return FeaturedProducts
     */
    public function getFeaturedProducts()
    {
        if (!$this->featuredProducts) {
            $this->featuredProducts = $this->featuredProductsFactory->create(['category' => $this->category]);
        }

        return $this->featuredProducts;
    }

    /**
     * @return \MageSuite\Navigation\Model\Navigation\ImageTeaser
     */
    public function getImageTeaser()
    {
        if (!$this->imageTeaser) {
            $this->imageTeaser = $this->imageTeaserFactory->create(['category' => $this->category]);
        }

        return $this->imageTeaser;
    }

    /**
     * @return bool
     */
    public function hasFeaturedProducts()
    {
        return !empty($this->getFeaturedProducts()->getProducts());
    }

    /**
     * @return bool
     */
    public function hasSubItems()
    {
        return !empty($this->getSubItems());
    }

    /**
     * @return bool
     */
    public function hasImageTeaser()
    {
        if ($this->category->getLevel() > 2) {
            return false;
        }

        return !empty($this->getImageTeaser()->getSlides());
    }

    /**
     * @return bool
     */
    public function hasCustomUrl()
    {
        return !empty($this->category->getCategoryCustomUrl());
    }

    /**
     * @return string|null
     */
    public function getCustomUrl()
    {
        return $this->category->getCategoryCustomUrl();
    }

    /**
     * @return bool
     */
    public function isUrlDisabled()
    {
        return (bool)$this->category->getUrlDisabled();
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return $this->category->getIdentities();
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoryIcon()
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

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface|\Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}

<?php

namespace MageSuite\Navigation\Model\Navigation;

class ImageTeaser extends \MageSuite\ContentConstructorFrontend\Model\Component\ImageTeaser
{
    public function __construct(
        protected \Magento\Catalog\Model\Category $category,
        \MageSuite\ContentConstructorFrontend\Model\Component\ImageTeaser\SlideFactory $slideFactory,
        protected \MageSuite\Navigation\Service\Mapper\CategoryMapper $categoryMapper,
        array $data = []
    ) {
        parent::__construct($slideFactory, $data);
    }

    public function getData($key = '', $index = null)
    {
        if ($key == 'items') {
            return $this->categoryMapper->mapCategory($this->getCategory());
        }

        return parent::getData($key, $index);
    }

    public function getCategory()
    {
        return $this->category;
    }
}

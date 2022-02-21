<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(555)
    ->setAttributeSetId(4)
    ->setName('First product')
    ->setSku('first_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(556)
    ->setAttributeSetId(4)
    ->setName('Second product')
    ->setSku('second_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(557)
    ->setAttributeSetId(4)
    ->setName('Third product')
    ->setSku('third_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$product->reindex();
$product->priceReindexCallback();

/** @var $category \Magento\Catalog\Model\Category */
$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category->setId(13)
    ->setName('Category do not expand flyout')
    ->setParentId(2)
    ->setPath('1/2/13')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setDoNotExpandFlyout(true)
    ->setPosition(100)
    ->save()
    ->reindex();

$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category->setId(14)
    ->setName('Category with custom url')
    ->setParentId(2)
    ->setPath('1/2/14')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setCategoryCustomUrl('testurl.html')
    ->setCategoryIdentifier('cat14')
    ->setPosition(101)
    ->save()
    ->reindex();

$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category->setId(15)
    ->setName('Category with featured products')
    ->setParentId(2)
    ->setPath('1/2/15')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setCategoryCustomUrl('/testurl.html')
    ->setCategoryIdentifier('cat15')
    ->setFeaturedProducts('{"555":"","556":"","557":""}')
    ->setFeaturedProductsHeader('Featured Products Header')
    ->setPosition(102)
    ->setImageTeaser('teaser.png')
    ->setImageTeaserSlogan('Image Teaser Slogan')
    ->setImageTeaserDescription('Image Teaser Description')
    ->setImageTeaserCtaLabel('Image Teaser Cta Label')
    ->setImageTeaserCtaLink('url')

    ->save()
    ->reindex();



$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category
    ->setId(16)
    ->setCreatedAt('2014-06-23 09:50:07')
    ->setName('Subcategory')
    ->setParentId(15)
    ->setPath('1/2/15/16')
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->setAvailableSortBy(['position'])
    ->setIncludeInMenu(1)
    ->setCategoryIcon('icon.png')
    ->setImageTeaser('teaser.png')
    ->setImageTeaserSlogan('Image Teaser Slogan')
    ->setImageTeaserCtaLabel('Image Teaser CTA Label')
    ->setImageTeaserCtaLink('url')
    ->save()
    ->reindex();

$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category->setId(17)
    ->setName('Category with custom url with directives')
    ->setParentId(2)
    ->setPath('1/2/17')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setCategoryCustomUrl('{{store url="url-to-some-nice-page"}}')
    ->setCategoryIdentifier('cat17')
    ->setPosition(105)
    ->setImageTeaser('teaser.png')
    ->setImageTeaserSlogan('Image Teaser Slogan')
    ->setImageTeaserDescription('Image Teaser Description')
    ->setImageTeaserCtaLabel('Image Teaser Cta Label')
    ->setImageTeaserCtaLink('url')

    ->save()
    ->reindex();

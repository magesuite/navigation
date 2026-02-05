<?php

declare(strict_types=1);

namespace MageSuite\Navigation\Test\Integration\Service\Navigation;

/**
 * @magentoAppArea frontend
 */
class BuilderTest extends \PHPUnit\Framework\TestCase
{
    protected const ROOT_CATEGORY_ID = 2;

    protected \Magento\TestFramework\ObjectManager $objectManager;
    protected \MageSuite\Navigation\Service\Navigation\Builder $builder;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->builder = $this->objectManager->get(\MageSuite\Navigation\Service\Navigation\Builder::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture MageSuite_Navigation::Test/Integration/_files/categories_not_included_in_menu.php
     * @magentoCache all disabled
     */
    public function testItReturnsNavigationCorrectStructure(): void
    {
        $navigation = $this->builder->build(self::ROOT_CATEGORY_ID);
        $this->assertCount(7, $navigation);
        $this->assertCount(1, $navigation[0]->getSubItems());
        $this->assertEquals('Category 1', $navigation[0]->getLabel());
        $this->assertEquals('Category 2', $navigation[1]->getLabel());
        $this->assertEquals('Category 1.1', $navigation[0]->getSubItems()[0]->getLabel());
        $this->assertEquals('Category 1.1.1', $navigation[0]->getSubItems()[0]->getSubItems()[0]->getLabel());
        $this->assertEquals(2, $navigation[0]->getParentId());
        $this->assertEquals(2, $navigation[0]->getProductCount());
        $this->assertEquals(0, $navigation[1]->getProductCount());
        $this->assertEquals('http://localhost/index.php/category-1.html', $navigation[0]->getUrl());
        $this->assertEquals('http://localhost/index.php/category-1/category-1-1.html', $navigation[0]->getSubItems()[0]->getUrl());
        $this->assertEquals('http://localhost/index.php/category-1/category-1-1/category-1-1-1.html', $navigation[0]->getSubItems()[0]->getSubItems()[0]->getUrl());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture MageSuite_Navigation::Test/Integration/_files/categories_not_included_in_menu.php
     */
    public function testItReturnsOnlyItemsForMobileNavigation(): void
    {
        $navigation = $this->builder->build(
            self::ROOT_CATEGORY_ID,
            \MageSuite\Navigation\Service\Navigation\Builder::TYPE_MOBILE
        );

        $this->assertCount(9, $navigation);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoAdminConfigFixture cc_frontend_extension/configuration/sort_alphabetically 1
     * @magentoDataFixture MageSuite_Navigation::Test/Integration/_files/categories_sorted_with_products.php
     */
    public function testItReturnsNavigationCorrectSorting(): void
    {
        $sortedCategories = $this->builder->build(3331);
        $this->assertEquals($sortedCategories[0]->getLabel(), 'Ä Fourth subcategory');
        $this->assertEquals($sortedCategories[1]->getLabel(), 'A Second subcategory');
        $this->assertEquals($sortedCategories[2]->getLabel(), 'B Third subcategory');
        $this->assertEquals($sortedCategories[3]->getLabel(), 'C First subcategory');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture MageSuite_Navigation::Test/Integration/_files/categories_with_custom_attributes.php
     */
    public function testItReturnsCategoriesWithCorrectAttributes(): void
    {
        $result = $this->builder->build(self::ROOT_CATEGORY_ID);
        $this->assertCount(11, $result);
        $this->assertEquals('http://localhost/index.php/testurl.html', $result[8]->getUrl());
        $this->assertEquals('http://localhost/index.php/testurl.html', $result[9]->getUrl());
        $this->assertEquals('cat14', $result[8]->getIdentifier());
        $this->assertEquals('cat15', $result[9]->getIdentifier());

        $featuredProducts = $result[9]->getFeaturedProducts();
        $this->assertEquals('Featured Products Header', $featuredProducts->getHeader());
        $this->assertCount(2, $featuredProducts->getProducts());
        $this->assertEquals('Second product', $featuredProducts->getProducts()[556]->getName());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture MageSuite_Navigation::Test/Integration/_files/categories_with_custom_attributes.php
     */
    public function testItReturnsNavigationWithImageTeaser(): void
    {
        $result = $this->builder->build(2);
        $navigationItem = $result[9];
        $this->assertEquals(15, $navigationItem->getId());

        $imageTeaser = $navigationItem->getImageTeaser();
        $this->assertTrue($navigationItem->hasImageTeaser());
        $this->assertCount(1, $navigationItem->getImageTeaser()->getSlides());

        $slide = $navigationItem->getImageTeaser()->getSlides()[0];

        $this->assertEquals('{{media url="catalog/category/teaser.png"}}', $slide->getImage()['decoded']);
        $this->assertEquals('Image Teaser Description', $slide->getDescription());
        $this->assertEquals('http://localhost/index.php/url', $slide->getCta()['href']);

        $navigationItem = $navigationItem->getSubItems()[0];
        $this->assertEquals(16, $navigationItem->getId());
        $this->assertFalse($navigationItem->hasImageTeaser());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoDataFixture MageSuite_Navigation::Test/Integration/_files/categories_with_custom_attributes.php
     */
    public function testItProcessesDirectivesProperly(): void
    {
        $result = $this->builder->build(2);
        $url = $result[10]->getUrl();
        $this->assertEquals('http://localhost/index.php/url-to-some-nice-page/', $url, 'Failed to assert that directives has been processed correctly.');
    }
}

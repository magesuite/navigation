<?php

declare(strict_types=1);

namespace MageSuite\Navigation\Test\Unit\Service\Category;

class CustomUrlGeneratorTest extends \PHPUnit\Framework\TestCase
{
    protected const string BASE_URL = 'https://example.com/';

    protected ?\MageSuite\Navigation\Service\Category\CustomUrlGenerator $generator;

    protected function setUp(): void
    {
        $storeManager = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $store = $this->createMock(\Magento\Store\Model\Store::class);
        $store->method('getBaseUrl')->willReturn(self::BASE_URL);
        $storeManager->method('getStore')->willReturn($store);

        $filter = $this->createMock(\Magento\Framework\Filter\Template::class);
        $filter->method('filter')->willReturnArgument(0);

        $filterProvider = $this->createMock(\Magento\Cms\Model\Template\FilterProvider::class);
        $filterProvider->method('getBlockFilter')->willReturn($filter);

        $logger = $this->createMock(\Psr\Log\LoggerInterface::class);

        $this->generator = new \MageSuite\Navigation\Service\Category\CustomUrlGenerator(
            $storeManager,
            $filterProvider,
            $logger
        );
    }

    public function testItResolvesRelativeUrlAgainstStoreBaseUrl(): void
    {
        $this->assertSame(self::BASE_URL . 'catalog/category/view/id/5', $this->generator->generate('/catalog/category/view/id/5'));
    }

    public function testItReturnsValidHttpsUrlUnchanged(): void
    {
        $url = 'https://external.example/landing-page';

        $this->assertSame($url, $this->generator->generate($url));
    }

    public function testItReturnsValidHttpUrlUnchanged(): void
    {
        $url = 'http://external.example/landing-page';

        $this->assertSame($url, $this->generator->generate($url));
    }

    public function testItReturnsProtocolRelativeUrlUnchanged(): void
    {
        $url = '//external.example/landing-page';

        $this->assertSame($url, $this->generator->generate($url));
    }

    public function testItRejectsJavascriptSchemeAndTreatsItAsRelative(): void
    {
        $url = 'javascript:alert(document.domain)';

        $this->assertSame(self::BASE_URL . $url, $this->generator->generate($url));
    }

    public function testItRejectsDataSchemeAndTreatsItAsRelative(): void
    {
        $url = 'data:text/html,<script>alert(1)</script>';

        $this->assertSame(self::BASE_URL . $url, $this->generator->generate($url));
    }

    public function testItStripsControlCharactersFromTheReturnedValue(): void
    {
        $url = "https://external.example/land\x00ing\x1F-page";

        $this->assertSame('https://external.example/landing-page', $this->generator->generate($url));
    }

    /**
     * CustomUrlGenerator only validates the URL scheme; it is not responsible for HTML-context escaping.
     * Quote characters are intentionally passed through unchanged here - every rendering sink
     * (navigation templates, image teaser CTA) MUST escape the value (e.g. via $block->escapeUrl())
     * before writing it into an href attribute. This test documents that contract explicitly so
     * escaping is never assumed to happen at this layer.
     */
    public function testItPassesThroughDoubleQuoteCharactersUnescaped(): void
    {
        $url = 'https://example.test" onmouseover="alert(document.domain)';

        $this->assertSame($url, $this->generator->generate($url));
    }

    public function testItPassesThroughSingleQuoteCharactersUnescaped(): void
    {
        $url = "https://example.test' onmouseover='alert(document.domain)";

        $this->assertSame($url, $this->generator->generate($url));
    }
}

<?php

namespace MageSuite\Navigation\Service\Category;

class CustomUrlGenerator
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->storeManager = $storeManager;
        $this->filterProvider = $filterProvider;
        $this->logger = $logger;
    }

    public function generate($url)
    {
        try{
            $url = $this->filterProvider->getBlockFilter()->filter($url);
        } catch (\Exception $e){
            $this->logger->critical(sprintf('Failed to filter URL: %s', $url));
        }

        $normalizedUrl = $this->normalizeUrl((string)$url);

        if ($this->isAllowedAbsoluteUrl($normalizedUrl)) {
            return $normalizedUrl;
        }

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        return $baseUrl . ltrim($normalizedUrl, '/');
    }

    protected function normalizeUrl(string $url): string
    {
        return preg_replace('/[\x00-\x1F\x7F]+/', '', $url);
    }

    protected function isAllowedAbsoluteUrl(string $url): bool
    {
        if (!preg_match('/^([a-zA-Z][a-zA-Z\d+\-.]*):/', $url, $matches)) {
            return strpos($url, '//') === 0;
        }

        return in_array(strtolower($matches[1]), ['http', 'https'], true);
    }
}

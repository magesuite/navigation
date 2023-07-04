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

        if (strpos($url, 'http') !== false) {
            return $url;
        }

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        return $baseUrl . ltrim($url, '/');
    }
}

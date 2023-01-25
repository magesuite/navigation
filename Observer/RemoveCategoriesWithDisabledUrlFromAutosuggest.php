<?php

declare(strict_types=1);

namespace MageSuite\Navigation\Observer;

class RemoveCategoriesWithDisabledUrlFromAutosuggest implements \Magento\Framework\Event\ObserverInterface
{
    protected \Magento\Framework\App\Request\Http $request;

    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if ($this->request->getFullActionName() != 'search_ajax_suggest') {
            return;
        }

        $observer->getCategoryCollection()->addAttributeToFilter(
            'url_disabled',
            [['neq' => 1], ['null' => true]],
            'left'
        );
    }
}

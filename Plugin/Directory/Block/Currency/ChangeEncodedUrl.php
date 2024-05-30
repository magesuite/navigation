<?php
declare(strict_types=1);

namespace MageSuite\Navigation\Plugin\Directory\Block\Currency;

class ChangeEncodedUrl
{
    protected \Magento\Framework\App\RequestInterface $request;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;
    protected \Magento\Framework\App\Response\RedirectInterface $redirect;
    protected \Magento\Framework\Url\Helper\Data $urlHelper;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Url\Helper\Data $urlHelper
    ) {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->redirect = $redirect;
        $this->urlHelper = $urlHelper;
    }

    public function afterGetSwitchCurrencyPostData(\Magento\Directory\Block\Currency $subject, $result)
    {
        if ($this->request->getFullActionName() !== 'navigation_mobile_index') {
            return $result;
        }

        $result = $this->serializer->unserialize($result);

        if (isset($result['data'][\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED])) {
            $result['data'][\Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED] = $this->urlHelper->getEncodedUrl($this->redirect->getRefererUrl());
        }

        return $this->serializer->serialize($result);
    }
}

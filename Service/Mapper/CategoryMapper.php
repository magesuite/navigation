<?php

declare(strict_types=1);

namespace MageSuite\Navigation\Service\Mapper;

class CategoryMapper
{
    protected ?\Magento\Catalog\Model\Category $category = null;

    protected ?string $rawImageUrl = null;

    public function __construct(
        protected \MageSuite\ContentConstructorFrontend\Service\MediaResolver $mediaResolver,
        protected \Magento\Store\Model\StoreManagerInterface $storeManager,
        protected \MageSuite\Navigation\Service\Category\CustomUrlGenerator $customUrlGenerator,
        protected \Magento\Framework\Escaper $escaper
    ) {}

    public function mapCategory(\Magento\Catalog\Model\Category $category): array
    {
        if (!$category->getImageTeaser()) {
            return [];
        }

        $this->category = $category;

        $slide = [
            'cta' => [
                'href' => $this->getCtaLink(),
                'label' => $this->getCtaLabel()
            ],
            'decodedImage' => $this->getDecodedImage(),
            'image' => [
                'raw' => $this->getImageUrl(),
                'decoded' => $this->getDecodedImage()
            ],
            'description' => $this->getDescription(),
            'slogan' => $this->getSlogan(),
            'image_alt' => $this->getImageAlt()
        ];

        return [$slide];
    }

    protected function getDecodedImage()
    {
        return sprintf('{{media url="%s"}}', $this->getRawImageUrl());
    }

    public function getImageUrl()
    {
        $rawImageUrl = $this->getRawImageUrl();

        if (empty($rawImageUrl)) {
            return null;
        }

        $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediaBaseUrl . $rawImageUrl;
    }

    public function getRawImageUrl()
    {
        if (!empty($this->rawImageUrl)) {
            $this->rawImageUrl;
        }

        $image = $this->category->getImageTeaser();
        $image = ltrim($image, '/');
        $image = str_replace('media/catalog/category/', '', $image);

        if (is_string($image)) {
            $this->rawImageUrl = 'catalog/category/' . $image;
        } elseif (is_array($image) && isset($image[0]) && isset($image[0]['name'])) {
            $this->rawImageUrl = 'catalog/category/' . $image[0]['name'];
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while getting the image url.')
            );
        }

        return $this->rawImageUrl;
    }

    /**
     * @return string
     */
    public function getSlogan()
    {
        return $this->category->getImageTeaserSlogan() ?? '';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->category->getImageTeaserDescription() ?? '';
    }

    public function getImageAlt(): string
    {
        return $this->category->getImageTeaserAlt() ?? '';
    }

    public function getCtaLabel(): string
    {
        return $this->escaper->escapeHtml($this->category->getImageTeaserCtaLabel() ?? '');
    }

    public function getCtaLink(): string
    {
        return $this->category->getImageTeaserCtaLink() ? $this->customUrlGenerator->generate($this->category->getImageTeaserCtaLink()) : '';
    }

    public function getSrcSet(): string
    {
        $imageTeaserUrl = $this->getImageUrl();

        return $imageTeaserUrl ? $this->mediaResolver->resolveSrcSetByDensity($imageTeaserUrl) : '';
    }
}

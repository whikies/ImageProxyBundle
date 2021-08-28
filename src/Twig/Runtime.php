<?php

namespace Rjd\ImageProxyBundle\Twig;

use Twig\Extension\RuntimeExtensionInterface;
use Rjd\ImageProxyBundle\Service\ImageProxyServiceInterface;

class Runtime implements RuntimeExtensionInterface
{
    /**
     * @var ImageProxyServiceInterface
     */
    private $imageProxyService;

    /**
     * @param ImageProxyServiceInterface $imageProxyService
     */
    public function __construct(ImageProxyServiceInterface $imageProxyService)
    {
        $this->imageProxyService = $imageProxyService;
    }

    public function imageProxyBasicUrl(string $url, array $params = [], bool $encrypt = false): ?string
    {
        return $this->imageProxyService->generateBasicUrl($url, $params, $encrypt);
    }

    public function imageProxyUrl(string $url, array $params = []): ?string
    {
        return $this->imageProxyService->generateUrl($url, $params);
    }

    public function imageProxyUrlImages(string $url, string $path): ?string
    {
        return $this->imageProxyService->generateUrlImages($url, $path);
    }

    public function imageProxyUrlOffersSmall(string $url): ?string
    {
        return $this->imageProxyService->generateUrlOffersSmall($ur);
    }

    public function imageProxyUrlOffersMedium(string $url): ?string
    {
        return $this->imageProxyService->generateUrlOffersMedium($ur);
    }

    public function imageProxyUrlOffersLarge(string $url): ?string
    {
        return $this->imageProxyService->generateUrlOffersLarge($ur);
    }

    public function imageProxyUrlOffersFull(string $url): ?string
    {
        return $this->imageProxyService->imageProxyUrlOffersFull($ur);
    }

    public function imageProxyEncrypt(string $value): ?string
    {
        return $this->imageProxyService->encrypt($value);
    }

    public function imageProxyDecrypt(string $value): ?string
    {
        return $this->imageProxyService->decrypt($value);
    }
}

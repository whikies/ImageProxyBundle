<?php

namespace Rjd\ImageProxyBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Extension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('image_proxy_basic_url', [Runtime::class, 'imageProxyBasicUrl']),
            new TwigFilter('image_proxy_url', [Runtime::class, 'imageProxyUrl']),
            new TwigFilter('image_proxy_url_images', [Runtime::class, 'imageProxyUrlImages']),
            new TwigFilter('image_proxy_url_offers_small', [Runtime::class, 'imageProxyUrlOffersSmall']),
            new TwigFilter('image_proxy_url_offers_medium', [Runtime::class, 'imageProxyUrlOffersMedium']),
            new TwigFilter('image_proxy_url_offers_large', [Runtime::class, 'imageProxyUrlOffersLarge']),
            new TwigFilter('image_proxy_encrypt', [Runtime::class, 'imageProxyEncrypt']),
            new TwigFilter('image_proxy_decrypt', [Runtime::class, 'imageProxyDecrypt']),
        ];
    }
}

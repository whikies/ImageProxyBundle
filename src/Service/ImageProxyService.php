<?php

namespace Rjd\ImageProxyBundle\Service;

use League\Uri\Components\HierarchicalPath;
use League\Uri\Http;

class ImageProxyService implements ImageProxyServiceInterface
{
    private const ENCRYPT_METHOD = 'AES-256-CBC';

    private const ENCRYPT_OPTIONS = OPENSSL_RAW_DATA;

    /**
     * @var string
     */
    private string $urlBase;

    /**
     * @var string
     */
    private string $hash;

    /**
     * @var array
     */
    private array $extensions = [
        'jpg',
        'jpeg',
        'gif',
        'png',
        'bmp'
    ];

    /**
     * @param string $urlBase
     * @param string $hash
     */
    public function __construct(string $urlBase, string $hash = '')
    {
        $this->urlBase = $urlBase;
        $this->hash = $hash;
    }

    /**
     * Genera la url de una imagen para conectar con el proxy
     *
     * @param string $url
     * @param array $params
     *
     * @return string|null
     */
    public function generateBasicUrl(string $url, array $params = [], bool $encrypt = false): ?string
    {
        if (empty($url) || empty($this->urlBase)) {
            return null;
        }

        $params['url'] = $encrypt ? $this->encrypt($url) : $url;

        $url = sprintf(
            '%s/img',
            rtrim($this->urlBase, '/')
        );

        $query = http_build_query($params);
        $url .= '?' . $query;

        return $url;
    }

    /**
     * Genera la url de una imagen para conectar con el proxy
     *
     * @param string $url
     * @param array $params
     *
     * @return string|null
     */
    public function generateUrl(string $url, array $params = []): ?string
    {
        if (empty($url) || empty($this->urlBase)) {
            return null;
        }

        $uri = Http::new($url);
        $hierarchicalPath = new HierarchicalPath($uri->getPath());
        $url = $this->normalizeScheme($url);

        $url = sprintf(
            '%s/img/%s.%s',
            rtrim($this->urlBase, '/'),
            urlencode($this->encrypt($url)),
            $hierarchicalPath->getExtension() && in_array(strtolower($hierarchicalPath->getExtension()), $this->extensions) ? $hierarchicalPath->getExtension() : 'png'
        );

        if ($params) {
            $query = http_build_query($params);
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * Genera una url encriptada con el path media para redimensionar
     *
     * @param string $url
     * @param int $width
     * @param int $height
     *
     * @return string|null
     */
    public function generateUrlMedia(string $url, int $width, int $height): ?string
    {
        if (empty($url) || empty($this->urlBase)) {
            return null;
        }

        $uri = Http::new($url);
        $hierarchicalPath = new HierarchicalPath($uri->getPath());
        $url = $this->normalizeScheme($url);
        $pathParams = '';

        $url = sprintf(
            '%s/media/%d/%d/%s.%s',
            rtrim($this->urlBase, '/'),
            $width,
            $height,
            urlencode($this->encrypt($url)),
            $hierarchicalPath->getExtension() && in_array(strtolower($hierarchicalPath->getExtension()), $this->extensions) ? $hierarchicalPath->getExtension() : 'png'
        );

        return $url;
    }

    /**
     * Genera una url encriptada con un path
     *
     * @param string $url
     * @param string $path
     *
     * @return string|null
     */
    public function generateUrlImages(string $url, string $path): ?string
    {
        if (empty($url) || empty($this->urlBase)) {
            return null;
        }

        $uri = Http::new($url);
        $hierarchicalPath = new HierarchicalPath($uri->getPath());
        $url = $this->normalizeScheme($url);
        $pathParams = '';

        $url = sprintf(
            '%s/%s%s.%s',
            rtrim($this->urlBase, '/'),
            $path ? $path . '/' : '',
            urlencode($this->encrypt($url)),
            $hierarchicalPath->getExtension() && in_array(strtolower($hierarchicalPath->getExtension()), $this->extensions) ? $hierarchicalPath->getExtension() : 'png'
        );

        return $url;
    }

    /**
     * Genera una url pequeña y encriptada de una oferta
     *
     * @param string $url
     *
     * @return string|null
     */
    public function generateUrlOffersSmall(string $url): ?string
    {
        return $this->generateUrlImages($url, 'offers/small');
    }

    /**
     * Genera una url pequeña y encriptada de una oferta
     *
     * @param string $url
     *
     * @return string|null
     */
    public function generateUrlOffersMedium(string $url): ?string
    {
        return $this->generateUrlImages($url, 'offers/medium');
    }

    /**
     * Genera una url pequeña y encriptada de una oferta
     *
     * @param string $url
     *
     * @return string|null
     */
    public function generateUrlOffersLarge(string $url): ?string
    {
        return $this->generateUrlImages($url, 'offers/large');
    }

    /**
     * Genera una url encriptada de una oferta con el mismo tamaño
     *
     * @param string $url
     *
     * @return string|null
     */
    public function imageProxyUrlOffersFull(string $url): ?string
    {
        return $this->generateUrlImages($url, 'offers/full');
    }

    /**
     * Encripta una cadena de texto
     *
     * @param string $value
     *
     * @return string|null
     */
    public function encrypt(string $value): ?string
    {
        $key = substr(md5($this->hash), 0, 16);
        $iv = substr(md5($key . $key), 0, 16);
        $value = @serialize($value);
        $encrypted = @base64_encode(@openssl_encrypt($value, static::ENCRYPT_METHOD, $key, static::ENCRYPT_OPTIONS, $iv));

        return $encrypted;
    }

    /**
     * Desencripta una cadena de texto
     *
     * @param string $value
     *
     * @return string|null
     */
    public function decrypt(string $value): ?string
    {
        $key = substr(md5($this->hash), 0, 16);
        $iv = substr(md5($key . $key), 0, 16);
        $decrypted = @openssl_decrypt(@base64_decode($value), static::ENCRYPT_METHOD, $key, static::ENCRYPT_OPTIONS, $iv);
        $decrypted = @unserialize($decrypted) ?: null;

        return $decrypted;
    }

    /**
     * Desencripta una cadena de texto y elimina la extensión
     *
     * @param string $value
     *
     * @return string|null
     */
    public function decryptAndRemoveExtension(string $value): ?string
    {
        $uri = Http::new($value);
        $hierarchicalPath = new HierarchicalPath($uri->getPath());

        if ($hierarchicalPath->getExtension()) {
            $value = str_replace('.' . $hierarchicalPath->getExtension(), '', $value);
        }

        return $this->decrypt($value);
    }

    /**
     * Elimina de una url el prefijo https por el de ssl:
     *
     * @param string $url;
     *
     * @return string
     */
    private function normalizeScheme(string $url): string
    {
        if (strpos($url, 'https:') === 0) {
            return 'ssl:' . ltrim(substr($url, 8), '/');
        } elseif (strpos($url, 'http:') === 0) {
            return ltrim(substr($url, 7), '/');
        }

        return $url;
    }
}

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
     * @var bool
     */
    private bool $encrypt;

    /**
     * @var string
     */
    private string $hash;

    /**
     * @param string $urlBase
     * @param bool $encrypt
     * @param string $hash
     */
    public function __construct(string $urlBase, bool $encrypt = false, string $hash = '')
    {
        $this->urlBase = $urlBase;
        $this->encrypt = $encrypt;
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
    public function generateUrl(string $url, array $params = [], string $path = null): ?string
    {
        if (empty($url) || empty($this->urlBase)) {
            return null;
        }

        $uri = Http::createFromString($url);

        dump($uri);
        return null;
        $hierarchicalPath = new HierarchicalPath($uri->getPath());

        $u = '';
        $u .= isset($url['scheme']) && $url['scheme'] == 'https' ? 'ssl:' : '';
        $u .= isset($url['host']) ? $url['host'] : '';
        $u .= isset($url['path']) ? $url['path'] : '';
        $u .= isset($url['query']) ? '?' . $url['query'] : '';

        $url = sprintf('%s%s%s', rtrim($this->urlBase, '/'), $path, urlencode($this->encrypt($u)));

        // if (!empty($info["extension"]) && 8 > strlen($info["extension"]) && strripos('?', $info["extension"]) === false) {
        //     $url .= '.' . $info["extension"];
        // } else {
        //     $url .= '.jpeg';
        // }

        if ($params) {
            $query = http_build_query($params);
            $url .= '?' . $query;
        }

        return $url;
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
        $iv  = substr(md5($key . $key), 0, 16);
        $value = @serialize($value);
        $encrypted = @base64_encode(@openssl_encrypt($value, static::ENCRYPT_METHOD, $key, static::ENCRYPT_OPTIONS, $iv));

        return $encrypted;
    }

    /**
     * Descripta una cadena de texto
     *
     * @param string $value
     *
     * @return string|null
     */
    public function decrypt(string $value): ?string
    {
        $key = substr(md5($key), 0, 16);
        $iv = substr(md5($key . $key), 0, 16);
        $decrypted = @openssl_decrypt(@base64_decode($value), static::ENCRYPT_METHOD, $key, static::ENCRYPT_OPTIONS, $iv);
        $decrypted = @unserialize($decrypted) ?: null;

        return $decrypted;
    }
}

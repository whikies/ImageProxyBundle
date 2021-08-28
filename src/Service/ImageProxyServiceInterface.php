<?php

namespace Rjd\ImageProxyBundle\Service;

interface ImageProxyServiceInterface
{
    /**
     * Genera la url de una imagen para conectar con el proxy
     *
     * @param string $url
     * @param array $params
     *
     * @return string|null
     */
    public function generateBasicUrl(string $url, array $params = [], bool $encrypt = false): ?string;

    /**
     * Genera la url de una imagen para conectar con el proxy
     *
     * @param string $url
     * @param array $params
     *
     * @return string|null
     */
    public function generateUrl(string $url, array $params = []): ?string;

    /**
     * Genera una url encriptada con un path
     *
     * @param string $url
     * @param string $path
     *
     * @return string|null
     */
    public function generateUrlImages(string $url, string $path): ?string;

    /**
     * Genera una url pequeña y encriptada de una oferta
     *
     * @param string $url
     *
     * @return string|null
     */
    public function generateUrlOffersSmall(string $url): ?string;

    /**
     * Genera una url pequeña y encriptada de una oferta
     *
     * @param string $url
     *
     * @return string|null
     */
    public function generateUrlOffersMedium(string $url): ?string;

    /**
     * Genera una url pequeña y encriptada de una oferta
     *
     * @param string $url
     *
     * @return string|null
     */
    public function generateUrlOffersLarge(string $url): ?string;

    /**
     * Encripta una cadena de texto
     *
     * @param string $value
     *
     * @return string|null
     */
    public function encrypt(string $value): ?string;

    /**
     * Desencripta una cadena de texto
     *
     * @param string $value
     *
     * @return string|null
     */
    public function decrypt(string $value): ?string;
}

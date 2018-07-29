<?php

namespace anvein\pipedrive_sdk;

use Exception;

/**
 * Class Uri
 * Класс для подготовки URI
 *
 * @package anvein\pipedrive_sdk
 */
class Uri
{
    /**
     * Протокол (схема)
     *
     * @var string
     */
    protected $protocol = '';

    /**
     * Userinfo
     *
     * @var array
     */
    protected $userInfo = [];

    /**
     * Хост
     *
     * @var string
     */
    protected $host = '';

    /**
     * Порт
     *
     * @var string
     */
    protected $port = '';

    /**
     * Путь
     *
     * @var string
     */
    protected $path = '';

    /**
     * Get-параметры (query / запрос)
     *
     * @var array
     */
    protected $query = [];

    /**
     * Якорь (фрагмент)
     *
     * @var string
     */
    protected $anchor = '';


    /**
     * Uri constructor.
     *
     * @param string|null $uri - полный URI
     */
    public function __construct(string $uri = null)
    {
        if (!is_null($uri)) {
            $this->parseUri($uri);
        }
    }


    /**
     * Возвращает протокол
     *
     * @param bool $isFull - если true, то вернет с ://
     *
     * @return int|null
     */
    public function getProtocol(bool $isFull = false)
    {
        if ($isFull && !empty($this->protocol)) {
            return $this->protocol . '://';
        }

        return $this->protocol;
    }

    /**
     * Задает протокол
     *
     * @param string $protocol
     *
     * @return Uri
     */
    public function setProtocol(string $protocol): self
    {
        $this->protocol = str_replace('://', '', trim($protocol));
        return $this;
    }

    /**
     * Возвращает массив с userInfo
     *
     * @return array
     */
    public function getUserInfo(): array
    {
        return $this->userInfo;
    }

    /**
     * Возвращает userInfo в виде подготовленной строки (как в URI)
     * user:password
     *
     * @return string
     */
    public function getUserInfoAsString(): string
    {
        if (!empty($this->userInfo)) {
            return "{$this->userInfo['login']}:{$this->userInfo['password']}@";
        }

        return '';
    }

    /**
     * @param string $login
     * @param string $password
     *
     * @throws Exception
     *
     * @return Uri
     */
    public function setUserInfo(string $login, string $password = ''): self
    {
        $this->userInfo = [
            'login'    => $login,
            'password' => $password
        ];
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return Uri
     */
    public function setHost(string $host): Uri
    {
        $this->host = str_replace('/', '', trim($host));
        return $this;
    }

    /**
     * Возвращает порт
     *
     * @param bool $forUri - если true, то вернет с двоеточием
     *
     * @return string
     */
    public function getPort(bool $forUri = false): string
    {
        if ($forUri && !empty($this->port)) {
            return ":{$this->port}";
        }

        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return Uri
     */
    public function setPort(int $port): self
    {
        $this->port = (string) $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Задает path
     *
     * @param string $path
     *
     * @return Uri
     */
    public function setPath(string $path): self
    {
        $path = trim($path);

        if (mb_substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }

        if (mb_substr($path, -1, 1) != '/') {
            $path .= '/';
        }

        $this->path = $path;
        return $this;
    }

    /**
     * Добавляет $path к концу path
     *
     * @param string $path
     *
     * @return Uri
     */
    public function addPath(string $path): self
    {
        $path = trim($path);

        if (mb_substr($path, 0, 1) == '/') {
            $path = mb_substr($path, 1);
        }
        if (mb_substr($path, -1, 1) != '/') {
            $path .= '/';
        }

        $this->path .= $path;
        return $this;
    }

    /**
     * Возвращает массив с get-параметрами (элементами query)
     *
     * @return array
     */
    public function getGetParams(): array
    {
        return $this->getParams;
    }

    /**
     * Задает ассоциативный массив get-параметров, где каждый элемент имеет:
     * ключ - (string) ключ get-параметра
     * значение - (string) значение get-параметра
     *
     * @param array $getParams
     *
     * @return Uri
     */
    public function setQueryFromArray(array $getParams): self
    {
        foreach ($getParams as $key => $value) {
            // TODO: почему-то если задаешь ключ число, как строку, то она преобразовывается в число
//            if (!is_string($key)) {
//                throw new Exception("Не задан ключ get-параметра {$value} или он не является строкой");
//            }

            $this->query[$key] = (string)$value;
        }

        return $this;
    }

    /**
     * Задает get-параметры (query) из строки query (?key=one&key=two)
     *
     * @param string $query
     *
     * @return Uri
     */
    public function setQueryFromString(string $query): self
    {
        $query = trim($query);
        $query = str_replace('?', '', $query);

        $arQuery = explode('&', $query);
        $arGetParams = [];
        foreach ($arQuery as $query) {
            $arGet = explode('=', $query);

            $arGetParams[$arGet[0]] = !empty($arGet[1])
                ? $arGet[1]
                : '';
        }

        $this->query = $arGetParams;
        return $this;
    }

    /**
     * Возвращает массив с get-параметрами
     *
     * @return array
     */
    public function getQueryAsArray(): array
    {
        return $this->getParams;
    }

    /**
     * Возвращает строку с get-параметрами
     *
     * @return string
     */
    public function getQueryAsString(): string
    {
        if (empty($this->query)) {
            return '';
        }

        $arQuery = [];
        foreach ($this->query as $key => $value) {
            $arQuery[] = "{$key}={$value}";
        }

        return '?' . implode('&', $arQuery);
    }

    /**
     * @return null
     */
    public function getAnchor(bool $forUri = false): string
    {
        if ($forUri && !empty($this->anchor)) {
            return '#' . $this->anchor;
        }

        return $this->anchor;
    }

    /**
     * @param null $anchor
     *
     * @return Uri
     */
    public function setAnchor(string $anchor): self
    {
        $anchor = str_replace('#', '', trim($anchor));

        $this->anchor = $anchor;
        return $this;
    }

    /**
     * Парсит URI в объект
     *
     * @param string $uri
     *
     * @throws Exception - URI не распознан
     *
     * @return void
     */
    protected function parseUri(string $uri): self
    {
        $arUriParts = parse_url($uri);

        if ($arUriParts === false) {
            throw new Exception('Не корректный URI');
        }

        if (!empty($arUriParts['scheme'])) {
            $this->setProtocol($arUriParts['scheme']);
        }

        if (!empty($arUriParts['user'])) {
            $this->setUserInfo($arUriParts['user'], $arUriParts['pass']);
        }

        if (!empty($arUriParts['host'])) {
            $this->setHost($arUriParts['host']);
        }

        if (!empty($arUriParts['port'])) {
            $this->setPort($arUriParts['port']);
        }

        if (!empty($arUriParts['path'])) {
            $this->setPath($arUriParts['path']);
        }

        if (!empty($arUriParts['query'])) {
            $this->setQueryFromString($arUriParts['query']);
        }

        if (!empty($arUriParts['fragment'])) {
            $this->setAnchor($arUriParts['fragment']);
        }

        return $this;
    }

    /**
     * Генерирует из имеющихся частей URI
     *
     * @return string - готовый URI
     */
    public function getUri(): string
    {
        $uri = '';
        $uri .= $this->getProtocol(true);
        $uri .= $this->getUserInfoAsString();
        $uri .= $this->getHost();
        $uri .= $this->getPort(true);
        $uri .= $this->getPath();
        $uri .= $this->getQueryAsString();
        $uri .= $this->getAnchor(true);

        return $uri;
    }

    /**
     * Печатает URI
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUri();
    }

}
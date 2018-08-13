<?php

namespace anvein\pipedrive_sdk;


use anvein\pipedrive_sdk\Entities\DealField;
use anvein\pipedrive_sdk\HttpClients\IHttpClient;
use anvein\pipedrive_sdk\Loggers\ILogger;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;

class Pipedrive
{
    /**
     * URI подключения API
     */
    const API_URI = 'https://api.pipedrive.com/v1/';

    /**
     * @var ILogger|null
     */
    protected $logger = null;

    /**
     * @var IHttpClient|null
     */
    protected $httpClient = null;

    /**
     * @var string
     */
    protected $token = '';

    /**
     * Включение режима отладки
     *
     * @var bool
     */
    protected $isDebug = false;

    /**
     * Сущности
     */
    public $user = null; // TODO: реальзовать user

    public $person = null; // TODO: реализовать person
    public $personField = null; // TODO: реализовать personFields

    public $deal = null; // TODO: реализовать deal

    /**
     * @var DealField|null
     */
    public $dealField = null; // TODO: реализовать dealFields

    public $note = null; // TODO: реализовать note
    public $noteField = null; // TODO: реализовать notrFields

    public $ogr = null; // TODO: реализовать organizations
    public $ogrField = null; // TODO: реализовать orgFields

    public $pipeline = null; // TODO: реализовать pipeline
    public $stage = null; // TODO: реализовать stage

    public $currency = null; // TODO: реализовать currencies

    // TODO: остальные сущности

    /**
     * Pipedrive constructor.
     *
     * @param string $token
     */
    public function __construct(string $token, ClientInterface $httpClient = null, ILogger $logger = null, bool $isDebug = false)
    {
        $this->token = $token;
        $this->isDebug = $isDebug;

        if (is_null($httpClient)) {
            $httpClient = new Client;
        }
        $this->httpClient = $httpClient;

        $this->dealField = new DealField($this);
    }

    /**
     * Возвращает токен подключения
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return IHttpClient
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @return ILogger
     */
    public function getLogger(): ILogger
    {
        return $this->logger;
    }

    /**
     * @return bool
     */
    public function getIsDebug(): bool
    {
        return $this->isDebug;
    }

}
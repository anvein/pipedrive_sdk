<?php

namespace anvein\pipedrive_sdk;


use anvein\pipedrive_sdk\Entities\DealField;
use anvein\pipedrive_sdk\Loggers\ILogger;
use anvein\pipedrive_sdk\HttpClients\IHttpClient;

class Pipedrive
{
    /**
     * URI подключения API
     */
    const CONNECT_URI = 'https://api.pipedrive.com/v1';


    /**
     * @var ILogger|null
     */
    protected $logger = null;

    /**
     * @var IHttpClient|null
     */
    protected $httpClient = null;


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
    public function __construct(string $token, IHttpClient $httpClient = null, ILogger $logger = null)
    {


        $this->dealField = new DealField($this);
    }

    /**
     * Возвращает токен подключения
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->getToken();
    }

    /**
     * @return IHttpClient|null
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return ILogger|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

}
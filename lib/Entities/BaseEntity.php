<?php

namespace anvein\pipedrive_sdk\Entities;

use anvein\pipedrive_sdk\Pipedrive;
use Psr\Http\Message\ResponseInterface;
use Exception;

/**
 * Class BaseEntity.
 */
abstract class BaseEntity
{
    /**
     * @var ClientInterface|null
     */
    protected $httpClient = null;

    /**
     * @var null
     */
    protected $logger = null;

    /**
     * @var string|null
     */
    protected $token = null;

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * DealField constructor.
     *
     * @param Pipedrive $pipe
     */
    public function __construct(Pipedrive $pipe)
    {
        $this->httpClient = $pipe->getHttpClient();
        $this->token = $pipe->getToken();
        $this->isDebug = $pipe->getIsDebug();
    }

    /**
     * Обрабатывает результат ответа от pipedrive.
     *
     * @param array $pipeResponse
     *
     * @return array
     *
     * @throws Exception
     */
    protected function handleResponse(ResponseInterface $pipeResponse)
    {
        $result = json_decode(
            $pipeResponse->getBody()->getContents(),
            true
        );

        if (!boolval($result['success']) && !empty($result['error'])) {
            throw new Exception($result['error']);
        } else {
            return $result['data'];
        }
    }

    /**
     * Подготавливает Request для Pipedrive и отправляет его.
     *
     * @param string $method
     * @param string $uri     - относительно /v1/
     * @param array  $query
     * @param array  $headers
     * @param array  $body
     *
     * @return ResponseInterface
     */
    protected function request(
        string $method,
        string $uri,
        array $query = [],
        array $headers = [],
        array $body = []
    ): ResponseInterface {
        $uri = Pipedrive::API_URI . $uri;
        $query['api_token'] = $this->token;

        return $this->httpClient->request(
            $method,
            $uri,
            [
                'json' => $body,
                'query' => $query,
                'headers' => $headers,
                'debug' => $this->isDebug ?: false,
                'http_errors' => false,
            ]
        );
    }

    /**
     * Отправляет запрос методом GET.
     *
     * @param string $uri     - относительно /v1/
     * @param array  $query
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    protected function getRequest(string $uri, array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(
            'GET',
            $uri,
            $query
        );
    }

    /**
     * Отправляет запрос методом POST.
     *
     * @param string $uri     - относительно /v1/
     * @param array  $body
     * @param array  $query
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    protected function postRequest(string $uri, array $body = [], array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(
            'POST',
            $uri,
            $query,
            $headers,
            $body
        );
    }

    /**
     * Отправляет запрос методом DELETE.
     *
     * @param string $uri     - относительно /v1/
     * @param array  $body
     * @param array  $query
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    protected function deleteRequest(string $uri, array $body = [], array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(
            'DELETE',
            $uri,
            $query,
            $headers,
            $body
        );
    }

    /**
     * Отправляет запрос методом PUT.
     *
     * @param string $uri     - относительно /v1/
     * @param array  $body
     * @param array  $query
     * @param array  $headers
     *
     * @return ResponseInterface
     */
    protected function putRequest(string $uri, array $body = [], array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(
            'PUT',
            $uri,
            $query,
            $headers,
            $body
        );
    }

    /**
     * Удаляет элементы null из массива
     *
     * @param array $arr
     *
     * @return array
     */
    protected function removeNullElements(array $arr = []): array
    {
        return array_diff($arr, [null]);
    }

}

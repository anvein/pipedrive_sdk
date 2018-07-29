<?php

namespace anvein\pipedrive_sdk\Entities;

use anvein\pipedrive_sdk\Pipedrive;


class DealField
{
    /**
     * @var Pipedrive|null
     */
    private $pipeInst = null;

    /**
     * DealField constructor.
     *
     * @param Pipedrive $pipe
     */
    public function __construct(Pipedrive $pipe)
    {
        $this->pipeInst = $pipe;
    }

    /**
     * Создает поле сделки
     *
     * @param string $name - имя поля
     * @param string $fieldType - тип поля
     * @param array $options - тип поля
     *
     * @return array|bool
     *
     * @throws Exception - если не указан параметр $email
     */
    public function createDealField(string $name, string $fieldType, array $options = [])
    {
        $http = new HttpClient();
        $http->post(
            "https://api.pipedrive.com/v1/dealFields?api_token={$this->token}",
            [
                'name' => $name,
                'field_type' => $fieldType,
                'options' => json_encode($options)
            ]
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data']['id'])) {
            return $result['data'];
        } else {
            return false;
        }
    }


    /**
     * Возвращает данные о поле сделки по ID
     *
     * @param int $id - id поля
     *
     * @return array
     * @throws Exception
     */
    public function getOne(int $id): array
    {
        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/dealFields/{$id}?api_token={$this->token}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['error'])) {
            throw new Exception($result['error']);
        } elseif (!empty($result['data'])) {
            return (array)$result['data'];
        } else {
            return [];
        }

    }


}
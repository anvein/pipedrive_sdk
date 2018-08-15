<?php

namespace anvein\pipedrive_sdk\Entities;

use Exception;

/**
 * Class Deal
 * API reference - https://developers.pipedrive.com/docs/api/v1/#!/Deals.
 */
class Deal
{
    /**
     * Создание сделки в pipedrive.
     *
     * В массив $dealFields можно передать:<br>
     * > title - заголовок сделки (обязательный)<br>
     * > value - стоимость сделки<br>
     * > person_id - пользователь с которой ассоциируется сделка<br>
     * > org_id - организация с которой ассоциируется сделка<br>
     * > stage_id - этап на который попадет сделка<br>
     * и прочие/кастомные поля.
     *
     * @param array $dealFields
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function createDeal(array $dealFields)
    {
        if (empty($dealFields['title'])) {
            throw new Exception('В массив $dealFields не передан обязательный элемент title');
        }

        $http = new HttpClient();
        $http->post(
            "https://api.pipedrive.com/v1/deals?api_token={$this->adminToken}",
            $dealFields
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (isset($result['data']['id'])) {
            return (int) $result['data']['id'];
        } else {
            return false;
        }
    }
}

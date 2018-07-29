<?php

namespace Creative\PipedriveModule;

use Bitrix\Main\Web\HttpClient;
use Creative\PipedriveModule\PipedriveHelper;
use Exception;

class Pipedrive
{


    /**
     * Возвращает всех пользователей, c почтой $email
     * @return array|bool - массив пользователей с почтой $email, или false
     * @throws Exception - если не указан параметр $email
     */
    public function getPersonListByEmail($email)
    {
        if (empty($email)) {
            throw new Exception('Не передан обязательный параметр $email');
        }
        $email = trim(strtolower($email));
        $emailForUrl = urlencode($email);

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/persons/find?term={$emailForUrl}&search_by_email=1&api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data'])) {
            return $result['data'];
        } else {
            return false;
        }
    }

    /**
     * Ищет пользователя по $email (даже если у него указано несколько email), <br>
     * если в результате несколько пользователей, то выбирает первого
     * @param $email
     * @throws Exception
     * @return array|bool - возвращает массив с данными найденного пользователя, либо false
     */
    public function getPersonByEmailExt($email)
    {
        if (empty($email)) {
            throw new Exception('Не передан обязательный параметр $email');
        }
        $email = trim(strtolower($email));

        $arPersons = $this->getPersonListByEmail($email);
        if (empty($arPersons)) {
            return false;
        }

        $arPersonsDetail = [];
        foreach ($arPersons as $person) {
            $bufPerson = $this->getPersonInfoById($person['id']);
            if (!empty($bufPerson)) {
                $arPersonsDetail[$person['id']] = $bufPerson;
            }
        }

        foreach ($arPersonsDetail as $person) {
            if (!empty($person['email'])) {
                foreach ($person['email'] as $personEmail) {
                    if (strtolower($personEmail['value']) === $email) {
                        return $person;
                    }
                }
            }
        }

        return false;
    }
    
    /**
     * Получает данные о пользователе
     * @param $personId - id персоны
     * @return bool - false, если персона не найдена
     * @return array - данные пользователя, в случае, если пользователь есть
     * @throws Exception - если не указан параметр $personId
     */
    public function getPersonInfoById($personId)
    {
        if (empty($personId)) {
            throw new Exception('Не передан обязательный параметр $personId');
        }

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/persons/{$personId}?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (isset($result['data']['id'])) {
            return $result['data'];
        } else {
            return false;
        }
    }

    /**
     * Создание пользователя в pipedrive
     * В массив $personFields можно передать:<br>
     * > name (string) [обязательный]<br>
     * > email (string)<br>
     * > phone (string)<br>
     * > org_id (int) - id организации, к которой отнести пользователя<br>
     *
     * @param array $personFields - массив с полями пользователя
     * @throws Exception
     * @return bool|int
     */
    public function createPerson(array $personFields)
    {
        if (empty($personFields['name'])) {
            throw new Exception('Не передан обязательный элемент name');
        }

        $http = new HttpClient();
        $http->post(
            "https://api.pipedrive.com/v1/persons?api_token={$this->adminToken}",
            $personFields
        );
        $result = json_decode(
            $http->getResult(),
            true
        );

        if (isset($result['data']['id'])) {
            return (int)$result['data']['id'];
        } else {
            return false;
        }
    }

    /**
     * Обновляет поля $personFields пользователя c ID $personId.
     * @param $personId - ID пользователя (обязательный)
     * @param array $personFields - массив полей пользователя, которые необходимо обновить (обязательный), если передать пустой массив
     * ничего не обновится
     * @throws Exception
     * @return bool - вернет false, если пользователь не обновился, true, если обновился
     */
    public function updatePerson($personId, array $personFields)
    {
        if (empty($personId)) {
            throw new Exception('Не передан обязательный параметр $personId');
        }

        if (empty($personFields)) {
            return false;
        }

        $http = new HttpClient();

        $http->query(
            HttpClient::HTTP_PUT,
            "https://api.pipedrive.com/v1/persons/{$personId}?api_token={$this->adminToken}",
            $personFields
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if ($result['success'] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Поиск организации по названию (точное совпадение)
     * @param $nameOrg - Название организации
     * @return bool|int - возвращает id организации, если она есть в пайпе, иначе false
     * @throws Exception - если не указан параметр $nameOrg
     */
    public function findOrganizationByName($nameOrg)
    {
        if (empty($nameOrg)) {
            throw new Exception('Не передан обязательный параметр $nameOrg');
        }
        $nameOrg = trim($nameOrg);

        $nameOrgForUrl = urlencode($nameOrg);
        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/organizations/find?term={$nameOrgForUrl}&start=0&api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (empty($result['data'])) {
            return false;
        }

        foreach ($result['data'] as $index => $element) {
            if (strtolower(trim($element['name'])) === strtolower($nameOrg)) {
                $resultId = $element['id'];
                break;
            }
        }

        if (isset($resultId)) {
            return $resultId;
        } else {
            return false;
        }
    }

    /**
     * Получает данные организации по ID
     * @param $orgId
     * @return bool
     * @throws Exception
     */
    public function getOrganizationInfoById($orgId)
    {
        if (empty($orgId)) {
            throw new Exception('Не передан обязательный параметр $orgId');
        }

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/organizations/{$orgId}?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (isset($result['data']['id'])) {
            return $result['data'];
        } else {
            return false;
        }
    }

    /**
     * Добавление организации
     * @param array $orgFields - массив полей организации. Должен обязательно содержать name - название организации.
     * Также может содержать пользовательские поля, у которых ключами должны быть хеши)
     * @return bool|int
     * @throws Exception - если не указан параметр name в массиве $orgFields
     */
    public function createOrganization(array $orgFields)
    {
        if (empty($orgFields['name'])) {
            throw new Exception('Не передан обязательный элемент name');
        }

        $http = new HttpClient();
        $http->post(
            "https://api.pipedrive.com/v1/organizations?api_token={$this->adminToken}",
            $orgFields
        );
        $result = json_decode(
            $http->getResult(),
            true
        );

        if (isset($result['data']['id'])) {
            return (int)$result['data']['id'];
        } else {
            return false;
        }
    }




    /**
     * Создание заметки в pipedrive
     *
     * В массив $noteFields можно передать поля:<br>
     * > content - текст заметки (required)<br>
     * > deal_id - id сделки к которой прикрепить заметку<br>
     * > person_id - id пользователя с котороым ассоциировать заметку<br>
     * > org_id - id организации с которой ассоциировать заметку<br>
     * @param array $noteFields
     * @throws Exception
     * @return bool
     */
    public function createNote(array $noteFields)
    {
        if (empty($noteFields['content'])) {
            throw new Exception('Не передан в массив $noteFields обязательный элемент content');
        }

        if (empty($noteFields['deal_id']) && empty($noteFields['person_id']) && empty($noteFields['org_id'])) {
            throw new Exception('Надо передать в массив $noteFields элемент deal_id/person_id/org_id');
        }

        $http = new HttpClient();
        $http->post(
            "https://api.pipedrive.com/v1/notes?api_token={$this->adminToken}",
            $noteFields
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (isset($result['data']['id'])) {
            return $result['data']['id'];
        } else {
            return false;
        }
    }

    /**
     * Возвращает этап $stageId
     * @param $stageId - id этапа
     * @throws Exception
     * @return array|bool - возвр. массив параметров этапа, если он существует, иначе false
     */
    public function getStageDetail($stageId)
    {
        if (empty($stageId)) {
            throw new Exception('Не передан обязательный параметр $stageId');
        }

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/stages/{$stageId}?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data']['id'])) {
            return (array)$result['data'];
        } else {
            return false;
        }
    }

    /**
     * Возвращает массив параметров этапов
     * @param string $pipelineId - id воронки к которой относятся запрашиваемые этапы (если не указан, вернет все этапы)
     * @return array|bool - возвр. массив полей этапов, если существует хотя бы один, иначе false
     */
    public function getStages($pipelineId = '')
    {
        $pipelineStr = '';
        if (!empty($pipelineId)) {
            $pipelineStr = "pipeline_id={$pipelineId}&";
        }

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/stages?{$pipelineStr}api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data'][0]['id'])) {
            return (array)$result['data'];
        } else {
            return false;
        }
    }


    /**
     * Возвращает список всех полей сделки
     * @return array|bool - массив с параметрами полями сделки, или false, если поля не найденый
     */
    public function getDealFields()
    {
        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/dealFields?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data'])) {
            return (array)$result['data'];
        } else {
            return false;
        }
    }


    /**
     * Возвращает параметры поля сделки по его id
     * @param $fieldId - id поля
     * @throws Exception
     * @return array|bool - массив с параметрами полей сделки, или false, если поле не найдено
     */
    public function getDealField($fieldId)
    {
        if (empty($fieldId)) {
            throw new Exception('Не передан обязательный параметр $fieldId');
        }

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/dealFields/{$fieldId}?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data']['id'])) {
            return (array)$result['data'];
        } else {
            return false;
        }
    }

    /**
     * Возвращает параметры поля пользователя по его id
     * @param $fieldId - id поля
     * @throws Exception
     * @return array|bool - массив с параметрами полей сделки, или false, если поле не найдено
     */
    public function getUserField($fieldId)
    {
        if (empty($fieldId)) {
            throw new Exception('Не передан обязательный параметр $fieldId');
        }

        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/personFields/{$fieldId}?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data']['id'])) {
            return (array)$result['data'];
        } else {
            return false;
        }

    }

    /**
     * Возвращает массив пользователей
     * @return array|bool - массив пользователей | false - если ничего не найдено
     */
    public function getUsersList()
    {
        $http = new HttpClient();
        $http->get(
            "https://api.pipedrive.com/v1/users?api_token={$this->adminToken}"
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data'])) {
            return (array)$result['data'];
        } else {
            return false;
        }
    }


    /**
     * Обновление данных о сделке
     * @param $dealId - id сделки в пайпе
     * @param array $dealUpdData - массив с полями, которые нужно изменить у сделки
     * @return bool - true, если сделка обновилать, иначе false
     */
    public function updateDeal($dealId, array $dealUpdData)
    {
        if (empty($dealUpdData)) {
            return false;
        }

        $http = new HttpClient();
        $http->query(
            HttpClient::HTTP_PUT,
            "https://api.pipedrive.com/v1/deals/{$dealId}?api_token={$this->adminToken}",
            $dealUpdData
        );

        $result = json_decode(
            $http->getResult(),
            true
        );

        if (!empty($result['data']['id'])) {
            return true;
        } else {
            return false;
        }
    }

}
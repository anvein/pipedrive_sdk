<?php

namespace anvein\pipedrive_sdk\Entities;

use Exception;

/**
 * Class Person
 *
 * @package anvein\pipedrive_sdk\Entities
 */
class Person extends BaseEntity
{

    /**
     * Ищет и возвращает контакты (Person) по имени
     *
     * @param string   $name - имя
     * @param int|null $orgId - ID организации
     *
     * @return array
     */
    public function getListByName(string $name, int $orgId = null): array
    {
        $name = strtolower(trim($name));

        $response = $this->getRequest(
            "persons/find",
            [
                'term' => $name,
                'search_by_email' => 0,
                'org_id' => $orgId,
            ]
        );

        return $this->handleResponse($response);
    }

    /**
     * Возвращает всех пользователей, c почтой $email
     *
     * @param string   $email - email
     * @param int|null $orgId - ID организации
     *
     * @return array
     */
    public function getListByEmail(string $email, int $orgId = null): array
    {
        $email = urlencode(strtolower(trim($email)));

        $response = $this->getRequest(
            "persons/find",
            [
                'term' => $email,
                'search_by_email' => 1,
                'org_id' => $orgId,
            ]
        );

        return $this->handleResponse($response);
    }

    /**
     * Получает все данные о контакте (Person).
     *
     * @param $id - id контакта
     *
     * @return array - данные пользователя, в случае, если пользователь есть
     */
    public function getOne(int $id): array
    {
        $response = $this->getRequest(
            "persons/{$id}"
        );

        return $this->handleResponse($response);
    }

    /**
     * Создание контакта
     *
     * @param string      $name - имя
     * @param array       $emails - массив email
     * @param array       $phones - массив телефонов
     * @param int|null    $orgId - ID организации
     * @param int|null    $ownerId - ID владельца
     * @param int|null    $visible - кому будет виден контакт (1 - все, 3 - владелец и подписчики)
     *
     * @throws Exception
     *
     * @return array - массив с данными нового пользователя
     */
    public function create(
        string $name,
        array $emails = [],
        array $phones = [],
        int $orgId = null,
        int $ownerId = null,
        int $visible = null
    ): array {
        if (!is_null($visible) && ($visible != 1 && $visible != 3)) {
            throw new Exception('Указано не верное значение параметра $visible, доступно 1 - public или 3 - private');
        }

        $response = $this->postRequest(
            'persons/',
            [
                'name'       => $name,
                'owner_id'   => $ownerId,
                'org_id'     => $orgId,
                'email'      => $emails,
                'phone'      => $phones,
                'visible_to' => $visible,
            ]
        );

        return $this->handleResponse($response);
    }

    /**
     * Обновляет поля контакта с ID - $id
     *
     * @param int         $id
     * @param string|null $name - имя
     * @param array       $emails - массив email
     * @param array       $phones - массив телефонов
     * @param int|null    $orgId - ID организации
     * @param int|null    $ownerId - ID владельца
     * @param int|null    $visible - кому будет виден контакт (1 - все, 3 - владелец и подписчики)
     *
     * @throws Exception
     *
     * @return array - массив новы данных контакта
     */
    public function update(
        int $id,
        string $name = null,
        array $emails = null,
        array $phones = null,
        int $orgId = null,
        int $ownerId = null,
        int $visible = null
    ): array {
        if (!is_null($visible) && ($visible != 1 && $visible != 3)) {
            throw new Exception('Указано не верное значение параметра $visible, доступно 1 - public или 3 - private');
        }

        $updateData = [
            'name'       => $name,
            'owner_id'   => $ownerId,
            'org_id'     => $orgId,
            'email'      => $emails,
            'phone'      => $phones,
            'visible_to' => $visible,
        ];
        $updateData = $this->removeNullElements($updateData);
        if (empty($updateData)) {
            throw new Exception('Хотя бы одно поле должно быть изменено');
        }

        $response = $this->putRequest(
            "persons/{$id}",
            $updateData
        );

        return $this->handleResponse($response);
    }

    




}
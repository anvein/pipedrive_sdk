<?php

namespace anvein\pipedrive_sdk\Entities;

use anvein\pipedrive_sdk\Pipedrive;
use GuzzleHttp\ClientInterface;
use Exception;

/**
 * Class DealField
 *
 * @package anvein\pipedrive_sdk\Entities
 */
class DealField extends BaseEntity
{
    /**
     * Возвращает данные о поле сделки по ID
     *
     * @param int $id - id поля
     *
     * @return array
     */
    public function getOne(int $id): array
    {
        $responce = $this->getRequest(
            "dealFields/{$id}/"
        );

        return $this->handleResponce($responce);
    }

    /**
     * Возвращает все поля сделки
     *
     * @return array
     */
    public function getAll(): array
    {
        $responce = $this->getRequest(
            'dealFields/'
        );

        return $this->handleResponce($responce);
    }

    /**
     * Создает поле сделки
     *
     * @param string $name      - имя поля
     * @param string $fieldType - тип поля
     * @param array  $options   - варианты значений поля для типов: enum, set
     *
     * @return array
     */
    public function create(string $name, string $fieldType, array $options = []): array
    {
        $responce = $this->postRequest(
            'dealFields/',
            [
                'name'       => $name,
                'field_type' => $fieldType,
                'options'    => $options,
            ]
        );

        return $this->handleResponce($responce);
    }

    /**
     * Обновляет поле сделки
     *
     * @param int $id - ID поля, которое надо изменить
     * @param string $name - новое имя поля
     * @param array $options - варианты значений поля для типов: enum, set
     *
     * @return array
     *
     */
    public function update(int $id, string $name, array $options = []): array
    {
        $responce = $this->putRequest(
            "dealFields/{$id}",
            [
                'name' => $name,
                'options' => $options,
            ]
        );

        return $this->handleResponce($responce);
    }

    /**
     * Удаляет поле сделки
     *
     * @param int $id - id удаляемого поля
     *
     * @return array
     */
    public function delete(int $id)
    {
        $this->deleteRequest(
            "dealFields/{$id}"
        );

        return $this->handleResponce($responce);
    }

    /**
     * Удаляет несколько полей сделки
     *
     * @param array $arIds
     *
     * @return array
     */
    public function deleteSeveral(array $arIds): array
    {
        $this->deleteRequest(
            'dealFields/',
            [],
            [
                'ids' => implode(',', $arIds),
            ]
        );

        return $this->handleResponce($responce);
    }

}
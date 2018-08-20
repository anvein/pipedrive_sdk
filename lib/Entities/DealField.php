<?php

namespace anvein\pipedrive_sdk\Entities;

/**
 * Class DealField.
 */
class DealField extends BaseEntity
{
    /**
     * Возвращает данные о поле сделки по ID.
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

        return $this->handleResponse($responce);
    }

    /**
     * Возвращает все поля сделки.
     *
     * @return array
     */
    public function getAll(): array
    {
        $responce = $this->getRequest(
            'dealFields/'
        );

        return $this->handleResponse($responce);
    }

    /**
     * Создает поле сделки.
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
                'name' => $name,
                'field_type' => $fieldType,
                'options' => $options,
            ]
        );

        return $this->handleResponse($responce);
    }

    /**
     * Обновляет поле сделки.
     *
     * @param int    $id      - ID поля, которое надо изменить
     * @param string $name    - новое имя поля
     * @param array  $options - варианты значений поля для типов: enum, set
     *
     * @return array
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

        return $this->handleResponse($responce);
    }

    /**
     * Удаляет поле сделки.
     *
     * @param int $id - id удаляемого поля
     *
     * @return array
     */
    public function delete(int $id)
    {
        $responce = $this->deleteRequest(
            "dealFields/{$id}"
        );

        return $this->handleResponse($responce);
    }

    /**
     * Удаляет несколько полей сделки.
     *
     * @param array $arIds
     *
     * @return array
     */
    public function deleteSeveral(array $arIds): array
    {
        $responce = $this->deleteRequest(
            'dealFields/',
            [],
            [
                'ids' => implode(',', $arIds),
            ]
        );

        return $this->handleResponse($responce);
    }
}

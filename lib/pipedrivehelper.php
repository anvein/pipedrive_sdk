<?php

namespace Creative\PipedriveModule;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Config\Option;

class PipedriveHelper
{
    /**
     * Возвращает случайный id модератора pipedrive из настроек
     * @return string - id модератора
     * @return false - если ни один id не указан в настройках
     */
    public static function getRandModeratorId()
    {
        $arOptUserIds = self::getListIdModerators();

        if (empty($arOptUserIds)) {
            return false;
        }

        return $arOptUserIds[array_rand($arOptUserIds)];
    }

    /**
     * Получаем список id модераторов из настроек
     * @return array
     */
    public static function getListIdModerators()
    {
        $arrTokens = [];

        for ($i = 1; $i < 11; $i++) {
            $valueOpt = trim(Option::get('creative.pipedrive', 'id_moderator_pipedrive_' . $i));
            if ($valueOpt !== '') {
                $arrTokens[] = $valueOpt;
            }
        }

        return $arrTokens;
    }

    /**
     * Получает токен администратора
     * @return bool - если не задан, то
     * @return string - если он задан, то возвр. токен
     */
    public static function getAdminToken()
    {
        $token = Option::get('creative.pipedrive', 'token_connection_pipedrive_admin');
        if (empty($token)) {
            return false;
        } else {
            return $token;
        }
    }

}
<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;

class FrontOffices extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule("iblock")) {
            $this->abortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }

        if (empty($this->arParams["CACHE_TIME"])) {
            $this->arParams["CACHE_TIME"] = 60 * 60 * 24;
        }

        $IBlockIdOffices = \ASH\Base\Helper::getIBlockId('offices', 2); //временно-намеренный маленькие кеш

        if ($this->startResultCache(false)) {

            $arSelect = array(
                "ID",
                "IBLOCK_ID",
                "NAME",
                "PROPERTY_FRONT_NAME_OFFICE",
                "PROPERTY_PHONE",
                "PROPERTY_EMAIL",
                "PROPERTY_CITY",
                "PROPERTY_COORDINATES",
            );

            $arFilter = array(
                "IBLOCK_ID" => $IBlockIdOffices,
                "ACTIVE" => "Y",
                array(
                    "LOGIC" => "OR",
                    array("<=DATE_ACTIVE_FROM" => ConvertTimeStamp(time(), "FULL")),
                    array("DATE_ACTIVE_FROM" => false),
                ),
                array(
                    "LOGIC" => "OR",
                    array(">=DATE_ACTIVE_TO" => ConvertTimeStamp(time(), "FULL")),
                    array("DATE_ACTIVE_TO" => false),
                ),
            );
            $res = \CIBlockElement::GetList(["SORT" => "ASC"], $arFilter, false, false, $arSelect);
            $arItems = array();
            while ($arItem = $res->fetch()) {
                $arItems[] = [
                    'name' => htmlspecialchars($arItem['PROPERTY_FRONT_NAME_OFFICE_VALUE']),
                    'phone' => htmlspecialchars($arItem['PROPERTY_PHONE_VALUE']),
                    'email' => htmlspecialchars($arItem['PROPERTY_EMAIL_VALUE']),
                    'city' => htmlspecialchars($arItem['PROPERTY_CITY_VALUE']),
                    'coordinates' => explode(',', $arItem['PROPERTY_COORDINATES_VALUE'], 2)
                ];
            }


            $this->arResult['ITEMS'] = $arItems;

            $this->SetResultCacheKeys(false);
            $this->includeComponentTemplate();
        }
    }
}
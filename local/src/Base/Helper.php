<?php

namespace ASH\Base;


class Helper
{
    /**
     * Get IblockID by code
     *
     * @param string $sCode
     * @return int
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIBlockId(string $sCode = '', $cacheTime = 86400): int
    {
        \Bitrix\Main\Loader::includeModule("iblock");
        $iID = null;
        $oCache = \Bitrix\Main\Data\Cache::createInstance();
        if ($oCache->initCache($cacheTime, 'iblock_id_g' . $sCode, '/')) {
            $iID = $oCache->getVars();
        } elseif ($oCache->startDataCache()) {
            $res = \CIBlock::GetList([], ['CODE' => $sCode, 'CHECK_PERMISSIONS' => 'N'], false);
            $ob = $res->GetNext();
            if ($ob) {
                $iID = (int)$ob['ID'];
            }
            $oCache->endDataCache($iID);
        }

        return (int)$iID;
    }
}
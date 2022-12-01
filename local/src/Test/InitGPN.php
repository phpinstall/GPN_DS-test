<?php

namespace ASH\Test;

class InitGPN
{

    private const IBLOCK_TYPE_ID = 'info';
    private const IBLOCK_CODE = 'offices';
    private const SITE_ID = 's1';

    private $report = [];
    private $iBlockId = null;

    private $testData = [
        [
            'name' => 'Санкт-Петербург, Киевская ул., 5, корп. 4',
            'city' => 'Санкт-Петербург',
            'email' => 'test@test.ru',
            'phone' => '+7 (812) 448-24-01',
            'coordinates' => [30.323836, 59.901761],
        ],
        [
            'name' => 'Москва, Научный пр., 17',
            'city' => 'Москва',
            'email' => 'example@example.ru',
            'phone' => '+7 (495) 514-03-68',
            'coordinates' => [37.555885, 55.654142],
        ],
        //следующие 3 на картах не обнаружены, список лишь с одними адресами предоставлен рекрутером
        [
            'name' => 'Москва, Территория ИЦ "Сколково", ул. Нобеля, д. 1',
            'city' => 'Москва',
            'email' => 'example@example.ru',
            'phone' => '+7 (812) 000-00-00',
            'coordinates' => [37.340897, 55.684426],
        ],
        [
            'name' => 'Санкт-Петербург, пер. Виленский, д. 14, лит А',
            'city' => 'Санкт-Петербург',
            'email' => 'example@example.ru',
            'phone' => '+7 (812) 000-00-00',
            'coordinates' => [30.369615, 59.940262],
        ],
        [
            'name' => 'Санкт-Петербург, ул. Льва Толстого, д. 1-3',
            'city' => 'Санкт-Петербург',
            'email' => 'example@example.ru',
            'phone' => '+7 (812) 000-00-00',
            'coordinates' => [30.312814, 59.965282],
        ]
    ];

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        \Bitrix\Main\Loader::includeModule('iblock');
    }

    /**
     * Основной вызов для установки тестовых данных
     */
    public function execute()
    {
        try {
            $this->createIBlockType()->createIBlock()->createIBlockProperties();
            $this->fillData();
        } catch (\Throwable $exception) {
            $this->report [] = 'Throwable: ' . $exception->getMessage();
        }
        $this->report();
    }

    /*
     * Создать тип инфоблока
     */
    private function createIBlockType(): InitGPN
    {
        global $DB;

        $issetIbType = \CIBlockType::GetByID(self::IBLOCK_TYPE_ID)->fetch();

        if (!$issetIbType) {
            $arFields = array(
                'ID' => self::IBLOCK_TYPE_ID,
                'SECTIONS' => 'Y',
                'SORT' => 100,
                'LANG' => array(
                    'en' => array(
                        'NAME' => 'Info',
                        'SECTION_NAME' => 'Sections',
                        'ELEMENT_NAME' => 'Elements'
                    ),
                    'ru' => array(
                        'NAME' => 'Информация',
                        'SECTION_NAME' => 'Раздел',
                        'ELEMENT_NAME' => 'Элемент'
                    )
                )
            );

            $obBlocktype = new \CIBlockType;
            $DB->StartTransaction();
            $res = $obBlocktype->Add($arFields);
            if (!$res) {
                $DB->Rollback();
                $this->report[] = 'Error: ' . $obBlocktype->LAST_ERROR;
                throw new \Exception('Невозможно создать тип инфоблока');
            } else {
                $DB->Commit();
                $this->report[] = 'Тип инфоблока: ' . $res . ' успешно создан';
            }
        } else {
            $this->report[] = "Тип инфоблока \"" . self::IBLOCK_TYPE_ID . "\" уже существует ";
        }
        return $this;
    }

    /**
     * Создать инфоблок
     *
     * @throws \Exception
     */
    private function createIBlock(): InitGPN
    {
        global $DB;
        $res = \CIBlock::GetList([], ['CODE' => self::IBLOCK_CODE, 'CHECK_PERMISSIONS' => 'N'], false);
        $IBlockId = $res->fetch()['ID'];
        if (!$IBlockId) {

            $arFields = array(
                "ACTIVE" => "Y",
                "NAME" => "Офисы",
                "CODE" => self::IBLOCK_CODE,
                "IBLOCK_TYPE_ID" => self::IBLOCK_TYPE_ID,
                "SITE_ID" => self::SITE_ID,
                "SORT" => "1",
                "VERSION" => 2, // отдельная
                "ELEMENT_NAME" => "Офис",
                "ELEMENTS_NAME" => "Офисы",
                "ELEMENT_ADD" => "Добавить офис",
                "ELEMENT_EDIT" => "Изменить офис",
                "ELEMENT_DELETE" => "Удалить офис",
            );

            $ib = new \CIBlock;
            $DB->StartTransaction();
            $IBlockId = $ib->Add($arFields);
            if ($IBlockId > 0) {
                $DB->Commit();
                $this->report[] = 'Инфоблок "Офисы" успешно создан';
            } else {
                $DB->Rollback();
                $this->report[] = 'Ошибка создания инфоблока "Офисы": ' . $ib->LAST_ERROR;
                throw new \Exception('Невозможно создать инфоблок');
            }
        } else {
            $this->report[] = "Инфоблок \"Офисы\" уже существует ";
        }
        $this->iBlockId = $IBlockId;

        return $this;
    }

    /*
     * Добавить свойства инфоблоку
     */
    private function createIBlockProperties(): InitGPN
    {
        $this->createIBlockProperty(array(
            'NAME' => 'Название офиса',
            'ACTIVE' => 'Y',
            'SORT' => '10',
            'CODE' => 'FRONT_NAME_OFFICE',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        $this->createIBlockProperty(array(
            'NAME' => 'Телефон',
            'ACTIVE' => 'Y',
            'SORT' => '20',
            'CODE' => 'PHONE',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        $this->createIBlockProperty(array(
            'NAME' => 'Email',
            'ACTIVE' => 'Y',
            'SORT' => '30',
            'CODE' => 'EMAIL',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        /* Город рациональнее сделать списком, или привязкой к другому инфоблоку с городами, но не буду усложнять себе сейчас жизнь */
        $this->createIBlockProperty(array(
            'NAME' => 'Город',
            'ACTIVE' => 'Y',
            'SORT' => '40',
            'CODE' => 'CITY',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
        ));

        /*
        $this->createIBlockProperty(array(
            'NAME' => 'Город',
            'ACTIVE' => 'Y',
            'SORT' => '40',
            'CODE' => 'CITY',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'Y',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => NULL,
            'USER_TYPE_SETTINGS' => NULL,
            'HINT' => '',
            'VALUES' =>
                array(),
        ));*/

        $this->createIBlockProperty(array(
            'NAME' => 'Координаты',
            'ACTIVE' => 'Y',
            'SORT' => '60',
            'CODE' => 'COORDINATES',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'map_yandex',
            'USER_TYPE_SETTINGS' =>
                array(),
            'HINT' => '',
        ));

        return $this;
    }


    private function createIBlockProperty($arFields): void
    {
        global $DB;

        $property = \CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => $this->iBlockId, "CODE" => $arFields['CODE']))->fetch();
        if (!$property) {

            $ibp = new \CIBlockProperty;
            $arFields['IBLOCK_ID'] = $this->iBlockId;
            $DB->StartTransaction();
            $propId = $ibp->Add($arFields);
            if ($propId > 0) {
                $DB->Commit();
                $this->report[] = "Добавлено свойство \"" . $arFields["NAME"] . "\"";
            } else {
                $DB->Rollback();
                $this->report[] = "Ошибка добавления свойства \"" . $arFields["NAME"] . "\": " . $ibp->LAST_ERROR;
            }

        } else {
            $this->report[] = "Свойство \"" . $arFields["NAME"] . "\" уже существует ";
        }

    }

    /*
     * Отчёт в браузер
     */
    private function report()
    {
        foreach ($this->report as $line) {
            echo $line . '<br>';
        }
    }

    /*
     * Заполнение тестовыми данными
     */
    private function fillData()
    {
        global $DB;
        $offices = [];
        $iterator = \CIBlockElement::getList(
            [],
            [
                'IBLOCK_ID' => $this->iBlockId,
            ],
            false,
            [],
            [
                'ID',
                'IBLOCK_ID',
                'NAME',
            ]
        );
        while ($row = $iterator->Fetch()) {
            $offices[] = $row;
        }

        $officesName = array_column($offices, 'NAME');

        $CIBlockElement = new \CIBlockElement;
        foreach ($this->testData as $insertData) {
            if (!in_array($insertData['name'], $officesName)) {
                $arFields = [
                    'IBLOCK_ID' => $this->iBlockId,
                    'NAME' => $insertData['name'],
                    'PROPERTY_VALUES' => [
                        'FRONT_NAME_OFFICE' => $insertData['name'],
                        'PHONE' => $insertData['phone'],
                        'EMAIL' => $insertData['email'],
                        'CITY' => $insertData['city'],
                        'COORDINATES' => $insertData['coordinates'][1] . ',' . $insertData['coordinates'][0]
                    ]
                ];
                $DB->StartTransaction();
                $elementId = $CIBlockElement->Add($arFields);
                if ($elementId > 0) {
                    $DB->Commit();
                    $this->report[] = "Добавлен элемент \"" . $insertData['name'] . "\"";
                } else {
                    $DB->Rollback();
                    $this->report[] = "Ошибка добавления элемента \"" . $insertData['name'] . "\": " . $CIBlockElement->LAST_ERROR;
                }
            } else {
                $this->report[] = "Элемент \"" . $insertData['name'] . "\" уже добавлен";
            }
        }
    }
}
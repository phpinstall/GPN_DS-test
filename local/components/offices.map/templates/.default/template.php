<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/*
    Укажите свой API-ключ. Тестовый ключ НЕ БУДЕТ работать на других сайтах.
    Получить ключ можно в Кабинете разработчика: https://developer.tech.yandex.ru/keys/
*/

$this->addExternalJS("https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=" . $arParams['YANDEX_KEY']);
?>
<style>
    #map-test {
        width: 100%;
        height: 777px;
        padding: 0;
        margin: 0;
    }
</style>

<div class="workarea">
    <div class="container bx-content-section">
        <div class="row">
            <div id="map-test"></div>
        </div>
    </div>
</div>

<script>

    ymaps.ready(init);

    function init() {
        let data = JSON.parse('<?=json_encode($arResult['ITEMS'])?>');

        let myMap = new ymaps.Map("map-test", {
            center: [58, 35],
            zoom: 6
        }, {
            searchControlProvider: 'yandex#search'
        });

        for (let k in data) {
            let text = '<strong>' + data[k]['name'] + '</strong>';
            if(data[k]['phone']){
                text += '<br><strong>Телефон:</strong> ' + data[k]['phone'];
            }
            if(data[k]['email']){
                text += '<br><strong>Email:</strong> ' + data[k]['email'];
            }
            myMap.geoObjects
                .add(new ymaps.Placemark([data[k]['coordinates'][0], data[k]['coordinates'][1]], {
                    balloonContent: text
                }, {
                    preset: 'islands#dotIcon',
                    iconColor: '#2341e2'
                }));
        }
    }
</script>
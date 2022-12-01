<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
/** @var $APPLICATION */
$APPLICATION->SetTitle("Офисы");
?>
<?php $APPLICATION->IncludeComponent(
	"offices.map", 
	".default", 
	array(
		"CACHE_TIME" => "20",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => ".default",
		"YANDEX_KEY" => ""
	),
	false
);?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>
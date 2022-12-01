<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("offices_map_list"),
	"ID" => "offices_map_list",
	"SORT" => 500,
	"PATH" => array(
		"ID" => "ash",
		"SORT" => 1,
		"NAME" => GetMessage("CONSTRUCTOR"),
	),
	"CACHE_PATH" => "Y",
);
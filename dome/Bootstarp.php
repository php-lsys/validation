<?php
include_once __DIR__."/../vendor/autoload.php";
function __(?string $string, array $values = NULL, string$domain = "default"):?string
{
		$i18n=\LSYS\I18n\DI::get()->i18n(__DIR__."/I18n/");
		return $i18n->__($string,  $values , $domain );
}

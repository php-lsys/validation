<?php
include_once __DIR__."/../vendor/autoload.php";
function __($string, array $values = NULL, $domain = "default")
{
		$i18n=\LSYS\I18n\DI::get()->i18n(__DIR__."/I18n/");
		return $i18n->__($string,  $values , $domain );
}

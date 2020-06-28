<?php
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    $load=require __DIR__ . '/../vendor/autoload.php';
} elseif (is_file(__DIR__ . '/../../../autoload.php')) {
    $load=require __DIR__ . '/../../../autoload.php';
} else {
    echo 'Cannot find the vendor directory, have you executed composer install?' . PHP_EOL;
    echo 'See https://getcomposer.org to get Composer.' . PHP_EOL;
    exit(1);
}

$load->setPsr4("TestLSYSVaild\\",[__DIR__."/classes/TestLSYSVaild/"]);

function __($string, array $values = NULL, $domain = "default")
{
    $i18n=\LSYS\I18n\DI::get()->i18n(__DIR__."/I18n/");
    return $i18n->__($string,  $values , $domain );
}

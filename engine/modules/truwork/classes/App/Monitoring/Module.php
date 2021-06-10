<?php


namespace App\Monitoring;


use App\Core\Entity\Module as BaseModule;

class Module extends BaseModule
{
    public const DATA_FILE = TW_DIR . '/cron/_monitoring_temp.php';

    public function register(): void
    {
        //
    }
}

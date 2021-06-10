<?php


namespace App\Cabinet\Middleware;


use App\Cabinet\Config;
use App\Core\Exceptions\Exception;
use App\Core\Http\Middleware\Middleware;
use App\Core\Http\Request;

class CheckModuleEnabled extends Middleware
{
    /**
     * @var Config
     */
    private $config;

    /**
     * CheckModuleEnabled constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Request $request
     * @param string $module
     * @throws Exception
     */
    public function handle(Request $request, string $module)
    {
        if (!$this->config->isEnabledModule($module)) {
            throw new Exception('Этот модуль отключен.');
        }
    }
}

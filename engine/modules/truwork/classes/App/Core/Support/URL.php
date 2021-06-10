<?php


namespace App\Core\Support;


class URL
{
    /**
     * URL constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    public static function create(string $module, string $controller = 'index', string $action = 'index', array $params = []): string
    {
        $data = [
            'mod' => 'truwork',
            'module' => $module
        ];

        if ($controller != 'index') {
            $data['control'] = $controller;
        }

        if ($action != 'index') {
            $data['action'] = $action;
        }

        return '?' . http_build_query(array_merge($data, $params));
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return string
     */
    public static function createBase(string $module, string $controller = 'index', string $action = 'index', array $params = []): string
    {
        $data = [
            'do' => 'tw',
            'module' => $module
        ];

        if ($controller != 'index') {
            $data['control'] = $controller;
        }

        if ($action != 'index') {
            $data['action'] = $action;
        }

        return '?' . http_build_query(array_merge($data, $params));
    }
}

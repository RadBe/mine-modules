<?php


namespace App;


class DLEConfig
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * DLEConfig constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @return string|numeric|null
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        $host = $this->get('http_home_url');
        if (empty($host) || $host == '/') {
            $host = $_SERVER['HTTP_HOST'] ?? '';
            if (strpos($host, 'http') === false) {
                $host = "http://$host";
            }
        }

        return $host;
    }
}

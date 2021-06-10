<?php


namespace App\Core\Config;


class Config
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Config constructor.
     *
     * @param array|string $data
     */
    public function __construct($data)
    {
        $this->data = is_array($data) ? $data : (array) json_decode($data, true);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->data);
    }
}

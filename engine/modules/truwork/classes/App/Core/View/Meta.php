<?php


namespace App\Core\View;


class Meta
{
    /**
     * @var array
     */
    private $data;

    /**
     * Meta constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->data['title'] = $title;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}

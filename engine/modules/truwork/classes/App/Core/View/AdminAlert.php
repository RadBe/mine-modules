<?php


namespace App\Core\View;


class AdminAlert
{
    public const MSG_TYPE_SUCCESS = 'success';

    public const MSG_TYPE_ERROR = 'error';

    public const MSG_TYPE_WARNING = 'warning';

    public const MSG_TYPE_INFO = 'info';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var bool|array|string
     */
    protected $back = false;

    /**
     * AdminAlert constructor.
     *
     * @param string $type
     * @param string $title
     * @param string $text
     */
    public function __construct(string $type, string $title, string $text)
    {
        $this->type = $type;
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * @param string $url
     * @param string $title
     * @return $this
     */
    public function withBack(string $url, string $title = 'Вернуться назад'): self
    {
        if (!is_array($this->back)) {
            $this->back = [];
        }

        $this->back[$url] = $title;

        return $this;
    }

    /**
     * @return void
     */
    public function render(): void
    {
        msg($this->type, $this->title, $this->text, $this->back);
    }
}

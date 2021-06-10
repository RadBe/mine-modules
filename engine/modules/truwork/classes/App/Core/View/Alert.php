<?php


namespace App\Core\View;


class Alert
{
    /**
     * @var bool
     */
    protected $status;

    /**
     * @var string
     */
    protected $message;

    /**
     * Alert constructor.
     *
     * @param bool $status
     * @param string $message
     */
    public function __construct(bool $status, string $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message
        ];
    }
}

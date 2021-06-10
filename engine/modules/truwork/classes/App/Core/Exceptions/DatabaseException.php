<?php


namespace App\Core\Exceptions;


class DatabaseException extends Exception
{
    /**
     * @var int|string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $msg;

    /**
     * @var string
     */
    private $query;

    /**
     * DatabaseException constructor.
     *
     * @param int|string $errorCode
     * @param string|null $msg
     * @param string $query
     */
    public function __construct($errorCode, ?string $msg, string $query)
    {
        parent::__construct("Error #($errorCode) $msg" . '. Query: ' . $query);

        $this->errorCode = $errorCode;
        $this->msg = (string) $msg;
        $this->query = $query;
    }

    /**
     * @return int|string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
}

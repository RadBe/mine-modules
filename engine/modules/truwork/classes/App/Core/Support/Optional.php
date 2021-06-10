<?php


namespace App\Core\Support;


use App\Core\Exceptions\Exception;

class Optional
{
    /**
     * @var object|null
     */
    private $object;

    /**
     * Optional constructor.
     *
     * @param object|null $object
     */
    public function __construct(?object $object)
    {
        $this->object = $object;
    }

    /**
     * @return bool
     */
    public function isPresent(): bool
    {
        return !is_null($this->object);
    }

    /**
     * @return object|null
     */
    public function get(): ?object
    {
        return $this->object;
    }

    /**
     * @param string|Exception $error
     * @return object
     * @throws Exception
     */
    public function getOrFail($error): object
    {
        if (!$this->isPresent()) {
            if ($error instanceof Exception) {
                throw $error;
            } else {
                throw new Exception($error);
            }
        }

        return $this->object;
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        return $this->isPresent() ? $this->object->$name : null;
    }

    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        return $this->isPresent() ? $this->object->$name($arguments) : null;
    }
}

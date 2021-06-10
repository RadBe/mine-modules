<?php


namespace App\Core\Http\Middleware;


use App\Core\Application;

abstract class Middleware
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Middleware constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @var string[]
     */
    private $except = [];

    /**
     * @var string[]
     */
    private $only = [];

    /**
     * @param string ...$actions
     * @return $this
     */
    public function except(string ...$actions): self
    {
        $this->except = array_merge($this->except, $actions);

        return $this;
    }

    /**
     * @param string ...$actions
     * @return $this
     */
    public function only(string ...$actions): self
    {
        $this->only = array_merge($this->only, $actions);

        return $this;
    }

    /**
     * @param string $action
     * @return bool
     */
    public function hasExcept(string $action): bool
    {
        return !empty($this->only) || in_array($action, $this->except);
    }

    /**
     * @param string $action
     * @return bool
     */
    public function hasOnly(string $action): bool
    {
        return in_array($action, $this->only);
    }
}

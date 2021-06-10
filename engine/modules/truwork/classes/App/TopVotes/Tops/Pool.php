<?php


namespace App\TopVotes\Tops;


use App\TopVotes\Exceptions\TopNotFoundException;

class Pool
{
    /**
     * @var Top[]
     */
    protected $tops;

    /**
     * Pool constructor.
     * @param Top[] $tops
     */
    public function __construct(array $tops)
    {
        $this->tops = $tops;
    }

    /**
     * @param string $name
     * @return Top
     * @throws TopNotFoundException
     */
    public function get(string $name): Top
    {
        foreach ($this->tops as $top)
        {
            if ($top->name() == $name) {
                return $top;
            }
        }

        throw new TopNotFoundException($name);
    }

    /**
     * @param Top $top
     */
    public function add(Top $top): void
    {
        $this->tops[] = $top;
    }

    /**
     * @return Top[]
     */
    public function all(): array
    {
        return $this->tops;
    }
}

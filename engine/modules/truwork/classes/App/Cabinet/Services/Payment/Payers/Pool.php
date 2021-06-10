<?php


namespace App\Cabinet\Services\Payment\Payers;


use App\Cabinet\Exceptions\PayerNotFoundException;

class Pool
{
    /**
     * @var Payer[]
     */
    protected $payers;

    /**
     * Pool constructor.
     *
     * @param Payer[] $payers
     */
    public function __construct(array $payers)
    {
        $this->payers = $payers;
    }

    /**
     * @param string $name
     * @return Payer|null
     */
    public function get(string $name): ?Payer
    {
        foreach ($this->payers as $payer)
        {
            if ($payer->name() == $name) {
                return $payer;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return Payer
     * @throws PayerNotFoundException
     */
    public function getOrFail(string $name): Payer
    {
        $payer = $this->get($name);
        if (is_null($payer)) {
            throw new PayerNotFoundException($name);
        }

        return $payer;
    }

    /**
     * @return Payer[]
     */
    public function all(): array
    {
        return $this->payers;
    }
}

<?php


namespace App\Shop\Events;


use App\Core\Entity\Server;
use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Shop\Entity\Product;

class BuyProductEvent extends LogEvent
{
    /**
     * @var Product
     */
    public $product;

    /**
     * BuyProductEvent constructor.
     *
     * @param User $user
     * @param Server $server
     * @param Product $product
     */
    public function __construct(User $user, Server $server, Product $product)
    {
        $this->user = $user;
        $this->server = $server;
        $this->product = $product;
    }

    /**
     * @inheritDoc
     */
    public function getLogType(): int
    {
        return 123456;// TODO:
    }
}

<?php


namespace App\Promo\Events;


use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Core\Support\Str;
use App\Promo\Entity\Promo;

class PromoActivateEvent extends LogEvent
{
    /**
     * @var Promo
     */
    public $promo;

    /**
     * PromoActivateEvent constructor.
     *
     * @param User $user
     * @param Promo $promo
     */
    public function __construct(User $user, Promo $promo)
    {
        $this->user = $user;
        $this->promo = $promo;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $rubWord = Str::declensionNumber($this->promo->amount, 'рубль', 'рубля', 'рублей');

        return sprintf('Активация промо-кода на сумму %d %s (%s)', $this->promo->amount, $rubWord, $this->promo->code);
    }
}

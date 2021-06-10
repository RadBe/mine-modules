<?php


namespace App\Cabinet\Events;


use App\Core\Entity\User;
use App\Core\Events\LogEvent;
use App\Core\Http\UploadedImage;

class SkinCloakUploadEvent extends LogEvent
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var UploadedImage
     */
    public $file;

    /**
     * @var bool
     */
    public $isHd;

    /**
     * SkinCloakUploadEvent constructor.
     *
     * @param User $user
     * @param string $type
     * @param UploadedImage $file
     * @param bool $isHd
     */
    public function __construct(User $user, string $type, UploadedImage $file, bool $isHd)
    {
        $this->user = $user;
        $this->type = $type;
        $this->file = $file;
        $this->isHd = $isHd;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return sprintf('Установка %s', $this->type == 'skin' ? 'скина' : 'плаща');
    }
}

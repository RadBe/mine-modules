<?php


namespace App\Core\Http;


class UploadedImage extends UploadedFile
{
    private const IMAGE_TYPES = ['gif', 'jpg', 'jpeg', 'png', 'webp'];

    /**
     * @var array
     */
    private $imageSize;

    /**
     * UploadedImage constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->imageSize = getimagesize($this->tmp);
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        if (!parent::isValid()) {
            return false;
        }

        return strpos($this->mimeType, 'image/') !== false
            && in_array($this->originalExtension, static::IMAGE_TYPES);
    }

    /**
     * @return array
     */
    public function getScale(): array
    {
        return $this->imageSize;
    }
}

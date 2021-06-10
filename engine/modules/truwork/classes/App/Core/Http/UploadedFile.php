<?php


namespace App\Core\Http;


use App\Core\Support\Str;
use Respect\Validation\Validator;

class UploadedFile
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $originalName;

    /**
     * @var string
     */
    protected $originalExtension;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $tmp;

    /**
     * @var int
     */
    protected $size;

    /**
     * UploadedFile constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $info = pathinfo($data['name']);
        $this->originalName = $info['filename'];
        $this->originalExtension = strtolower($info['extension']);
        $this->mimeType = $data['type'];
        $this->tmp = $data['tmp_name'];
        $this->size = $data['size'];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getOriginalExtension(): string
    {
        return $this->originalExtension;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getTmp(): string
    {
        return $this->tmp;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->data['error'] === UPLOAD_ERR_OK && $this->size > 0;
    }

    /**
     * @param string $directory
     * @param string|null $newName
     * @param string|null $extension
     * @return string|null
     */
    public function move(string $directory, ?string $newName = null, ?string $extension = null): ?string
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777);
        }

        $extension = $extension ?: $this->originalExtension;
        $fileName = $newName ?: Str::random(6) . '_' . Str::random(6) . '.' . ltrim($extension, '.');
        if (move_uploaded_file($this->tmp, rtrim($directory, '/') . '/' . $fileName)) {
            return $fileName;
        }

        return null;
    }

    /**
     * @param Validator $rules
     */
    public function validate(Validator $rules): void
    {
        $rules->assert($this->tmp);
    }
}

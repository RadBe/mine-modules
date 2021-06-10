<?php


namespace App\Core\VK\Events;


class WallPostNew extends Event
{
    /**
     * Получить id записи в ВК.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->data['id'];
    }

    /**
     * Получить id стены в ВК.
     *
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->data['owner_id'];
    }

    /**
     * Получить id автора в ВК.
     *
     * @return int
     */
    public function getFromId(): int
    {
        return $this->data['from_id'];
    }

    /**
     * Получить текст записи.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->data['text'];
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        return (array) ($this->data['attachments'] ?? []);
    }

    /**
     * @return string|null
     */
    public function getMainPhoto(): ?string
    {
        $photo = null;

        $attachments = $this->getAttachments();
        foreach ($attachments as $attachment)
        {
            if ($attachment['type'] == 'photo') {
                $photo = $attachment['photo'];
                $width = 0;
                foreach ($photo['sizes'] as $size)
                {
                    if ($size['width'] > $width) {
                        $width = $size['width'];
                        $photo = $size['url'];
                    }
                }
            }
        }

        return $photo;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return "https://vk.com/wall-{$this->getGroupId()}_{$this->getId()}";
    }
}

<?php


namespace App\Core\Services;


use App\Core\Cache\Cache;

final class SkinManager
{
    public const DIRECTORY = '/uploads/skins';

    public const MODE_FRONT = 0;

    public const MODE_BACK = 1;

    public const MODE_HEAD = 2;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $skinPath;

    /**
     * @var string|null
     */
    private $cloakPath;

    /**
     * Viewer constructor.
     *
     * @param string|null $username
     */
    public function __construct(?string $username)
    {
        $this->username = empty($username) ? 'default' : $username;

        $this->initPaths();
    }

    /**
     * @return void
     */
    private function initPaths(): void
    {
        $this->skinPath = static::getSkinFile($this->username);
        if (!is_file($this->skinPath)) {
            $this->skinPath = static::getSkinFile('default');
            $this->username = 'default';
        }

        $this->cloakPath = static::getCloakFile($this->username);
        if (!is_file($this->cloakPath)) {
            $this->cloakPath = null;
        }
    }

    /**
     * @param bool $fullPath
     * @return string
     */
    public static function getDirectory(bool $fullPath): string
    {
        return ($fullPath ? ROOT_DIR : '') . static::DIRECTORY;
    }

    /**
     * @param string $username
     * @param bool $fullPath
     * @return string
     */
    public static function getSkinFile(string $username, bool $fullPath = true): string
    {
        return static::getDirectory($fullPath) . '/skins/' . $username . '.png';
    }

    /**
     * @param string $username
     * @param bool $fullPath
     * @return string
     */
    public static function getCloakFile(string $username, bool $fullPath = true): string
    {
        return static::getDirectory($fullPath) . '/cloaks/' . $username . '.png';
    }

    /**
     * @param $result
     * @param $img
     * @param int $rx
     * @param int $ry
     * @param int $x
     * @param int $y
     * @param null $size_x2
     * @param null $size_y2
     * @param null $size_x
     * @param null $size_y
     */
    private function imageFlip(&$result, &$img, $rx = 0, $ry = 0, $x = 0, $y = 0, $size_x2 = null, $size_y2 = null, $size_x = null, $size_y = null)
    {
        if ($size_x < 1) {
            $size_x = imagesx($img);
        }

        if ($size_y < 1) {
            $size_y = imagesy($img);
        }

        imagecopyresampled($result, $img, $rx, $ry, ($x + $size_x - 1), $y, $size_x2, $size_y2, -$size_x, $size_y);
    }

    /**
     * @param string $username
     */
    public static function clearCache(string $username): void
    {
        $cache = Cache::skin();
        $cache->forget($username . static::MODE_FRONT);
        $cache->forget($username . static::MODE_BACK);
        $cache->forget($username . static::MODE_HEAD);
    }

    /**
     * @param int $mode
     */
    public function render(int $mode)
    {
        if ($mode == static::MODE_HEAD) {
            $this->renderHead();
        }

        $content = Cache::skin()->remember($this->username . $mode, function () use ($mode) {
            $zoom = 1;

            $skin = imagecreatefrompng($this->skinPath);
            $cloak = is_null($this->cloakPath) ? null : imagecreatefrompng($this->cloakPath);
            [$h] = getimagesize($this->skinPath);
            $ratio = $h / 64;

            if(!is_null($this->cloakPath)) {
                [$h2] = getimagesize($this->cloakPath);

                $ratio2 = $h2 / 64;

                if ($h2 == 22) {
                    $ratio2 = 1;
                }

                if ($ratio > $ratio2) {
                    $ration = $ratio;
                } else {
                    $ration = $ratio2;
                }
            } else {
                $ration = $ratio;
                $ratio2 = 0;
            }

            $preview = imagecreatetruecolor(16 * $ration, 32 * $ration);
            $alpha = imagecolorallocatealpha($preview, 255, 255, 255, 127);
            imagefill($preview, 0, 0, $alpha);

            if ($mode == static::MODE_FRONT) {
                if (!is_null($this->cloakPath)) {
                    imagecopyresampled($preview, $cloak, 3 * $ration, 8 * $ration, 12 * $ratio2, 1 * $ratio2, 10 * $ration, 16 * $ration, 10 * $ratio2, 16 * $ratio2);
                }

                imagecopyresampled($preview, $skin, 4 * $ration, 0 * $ration, 8 * $ratio, 8 * $ratio, 8 * $ration, 8 * $ration, 8 * $ratio, 8 * $ratio);

                imagecopyresampled($preview, $skin, 4 * $ration, 8 * $ration, 20 * $ratio, 20 * $ratio, 8 * $ration, 12 * $ration, 8 * $ratio, 12 * $ratio);

                imagecopyresampled($preview, $skin, 0 * $ration, 8 * $ration, 44 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
                $this->imageFlip($preview, $skin, 12 * $ration, 8 * $ration, 44 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);

                imagecopyresampled($preview, $skin, 4 * $ration, 20 * $ration, 4 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
                $this->imageFlip($preview, $skin, 8 * $ration, 20 * $ration, 4 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
            } else {
                imagecopyresampled($preview, $skin, 4 * $ration, 0 * $ration, 24 * $ratio, 8 * $ratio, 8 * $ration, 8 * $ration, 8 * $ratio, 8 * $ratio);

                imagecopyresampled($preview, $skin, 4 * $ration, 8 * $ration, 32 * $ratio, 20 * $ratio, 8 * $ration, 12 * $ration, 8 * $ratio, 12 * $ratio);

                imagecopyresampled($preview, $skin, 12 * $ration, 8 * $ration, 52 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
                $this->imageFlip($preview, $skin, 0 * $ration, 8 * $ration, 52 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);

                imagecopyresampled($preview, $skin, 8 * $ration, 20 * $ration, 12 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);
                $this->imageFlip($preview, $skin, 4 * $ration, 20 * $ration, 12 * $ratio, 20 * $ratio, 4 * $ration, 12 * $ration, 4 * $ratio, 12 * $ratio);

                if (!is_null($this->cloakPath)) {
                    imagecopyresampled($preview, $cloak, 3 * $ration, 8 * $ration, 1 * $ratio2, 1 * $ratio2, 10 * $ration, 16 * $ration, 10 * $ratio2, 16 * $ratio2);
                }
            }

            $fullsize = imagecreatetruecolor(90 * $zoom, 180 * $zoom);

            imagesavealpha($fullsize, true);
            $alpha = imagecolorallocatealpha($fullsize, 255, 255, 255, 127);
            imagefill($fullsize, 0, 0, $alpha);

            imagecopyresized($fullsize, $preview, 0, 0, 0, 0, imagesx($fullsize), imagesy($fullsize), imagesx($preview), imagesy($preview));

            ob_start();
            imagepng($fullsize);
            imagedestroy($preview);
            imagedestroy($fullsize);
            imagedestroy($skin);

            if(!is_null($this->cloakPath)) {
                imagedestroy($cloak);
            }

            return ob_get_clean();
        });

        header('Content-type: image/png');
        print $content;
        die;
    }

    /**
     * @return void
     */
    public function renderHead(): void
    {
        $content = Cache::skin()->remember($this->username . static::MODE_HEAD, function () {
            $size = 32;

            $skin = imagecreatefrompng($this->skinPath);
            [$h] = getimagesize($this->skinPath);
            $ratio = $h / 64;

            $preview = imagecreatetruecolor($size, $size);
            imagecopyresized($preview, $skin, 0 * $ratio, 0 * $ratio, 8 * $ratio, 8 * $ratio, $size, $size, 8 * $ratio, 8 * $ratio);

            ob_start();
            imagepng($preview);
            imagedestroy($skin);
            imagedestroy($preview);

            return ob_get_clean();
        });

        header('Content-type: image/png');
        print $content;
        die;
    }
}

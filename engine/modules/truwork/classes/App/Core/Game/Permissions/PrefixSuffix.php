<?php


namespace App\Core\Game\Permissions;


class PrefixSuffix
{
    /**
     * @var string
     */
    private $prefixColor;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $nickColor;

    /**
     * @var string
     */
    private $textColor;

    /**
     * @param string $prefix
     * @param string $suffix
     * @return PrefixSuffix
     */
    public static function createFromPermission(string $prefix, string $suffix): PrefixSuffix
    {
        /*
         * prefix format: &f[&4Admin&f]&a
         * prefix_color[1]: 4
         * prefix_text[2]: Admin
         * nick_color[3]: a
         */
        preg_match_all('/\[\&([a-f0-9])(.*)?\&[a-f0-9]\]\&([a-f0-9])/', $prefix, $pregPrefix);
        if (empty($pregPrefix[1]) && empty($pregPrefix[2]) && empty($pregPrefix3)) {
            preg_match_all('/\&([a-f0-9])/', $prefix, $pregPrefix);
            if (!empty($pregPrefix[1])) {
                $pregPrefix = [[], [], [], $pregPrefix[1]];
            }
        }

        $suffix = str_replace('&', '', $suffix);

        return new PrefixSuffix(
            @$pregPrefix[1][0] !== null && @$pregPrefix[1][0] !== '' ? $pregPrefix[1][0] : 'f',
            @$pregPrefix[2][0] !== null && @$pregPrefix[2][0] !== '' ? $pregPrefix[2][0] : '',
            @$pregPrefix[3][0] !== null && @$pregPrefix[3][0] !== '' ? $pregPrefix[3][0] : 'f',
            $suffix !== null && $suffix !== '' ? $suffix : '7');
    }

    /**
     * @return PrefixSuffix
     */
    public static function createEmpty(): PrefixSuffix
    {
        return new PrefixSuffix('f', '', 'f', '7');
    }

    /**
     * Prefix constructor.
     *
     * @param string $prefixColor
     * @param string $prefix
     * @param string $nickColor
     * @param string $textColor
     */
    public function __construct(string $prefixColor, string $prefix, string $nickColor = 'f', string $textColor = '7')
    {
        $this->prefixColor = $prefixColor;
        $this->prefix = $prefix;
        $this->nickColor = $nickColor;
        $this->textColor = $textColor;
    }

    /**
     * @return string
     */
    public function getPrefixColor(): string
    {
        return $this->prefixColor;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getNickColor(): string
    {
        return $this->nickColor;
    }

    /**
     * @return string
     */
    public function getTextColor(): string
    {
        return $this->textColor;
    }

    /**
     * @return string
     */
    public function prefixToPermissionFormat(): string
    {
        if (empty(trim($this->prefix))) {
            return "&{$this->nickColor}";
        }

        $prefix = '';
        if (!empty(trim($this->prefix))) {
            $prefix = "&f[&{$this->prefixColor}{$this->prefix}&f]";
        }

        return "$prefix&{$this->nickColor}";
    }

    /**
     * @return string
     */
    public function suffixToPermissionFormat(): string
    {
        return "&{$this->textColor}";
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'prefix_color' => $this->prefixColor,
            'prefix' => $this->prefix,
            'nick_color' => $this->nickColor,
            'text_color' => $this->textColor
        ];
    }
}

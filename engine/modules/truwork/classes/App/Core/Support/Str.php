<?php


namespace App\Core\Support;


class Str
{
    /**
     * Str constructor.
     */
    private function __construct()
    {
    }

    /**
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * @var array
     */
    protected static $camelCache = [];

    /**
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * @var callable
     */
    protected static $uuidFactory;

    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function after(string $subject, string $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function afterLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, (string) $search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }

    /**
     * @param  string  $value
     * @param  string  $language
     * @return string
     */
    public static function ascii($value, $language = 'en')
    {
        $languageSpecific = static::languageSpecificCharsArray($language);

        if (! is_null($languageSpecific)) {
            $value = str_replace($languageSpecific[0], $languageSpecific[1], $value);
        }

        foreach (static::charsArray() as $key => $val) {
            $value = str_replace($val, $key, $value);
        }

        return preg_replace('/[^\x20-\x7E]/u', '', $value);
    }

    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function before($subject, $search)
    {
        return $search === '' ? $subject : explode($search, $subject)[0];
    }

    /**
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function beforeLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return static::substr($subject, 0, $pos);
    }

    /**
     * @param  string  $value
     * @return string
     */
    public static function camel($value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string  $haystack
     * @param  string[]  $needles
     * @return bool
     */
    public static function containsAll($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if (! static::contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string  $value
     * @param  string  $cap
     * @return string
     */
    public static function finish($value, $cap)
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }

    /**
     * @param  string  $value
     * @return bool
     */
    public static function isUuid($value)
    {
        if (! is_string($value)) {
            return false;
        }

        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
    }

    /**
     * @param  string  $value
     * @return string
     */
    public static function kebab($value)
    {
        return static::snake($value, '-');
    }

    /**
     * @param  string  $value
     * @param  string|null  $encoding
     * @return int
     */
    public static function length($value, $encoding = null)
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    /**
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }

    /**
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * @param  string  $value
     * @param  int  $words
     * @param  string  $end
     * @return string
     */
    public static function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (! isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

    /**
     * @param  string  $callback
     * @param  string|null  $default
     * @return array<int, string|null>
     */
    public static function parseCallback($callback, $default = null)
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }

    /**
     * @param  int  $length
     * @return string
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * @param  string  $search
     * @param  array<int|string, string>  $replace
     * @param  string  $subject
     * @return string
     */
    public static function replaceArray($search, array $replace, $subject)
    {
        $segments = explode($search, $subject);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= (array_shift($replace) ?? $search).$segment;
        }

        return $result;
    }

    /**
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $subject
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $subject
     * @return string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * @param  string  $value
     * @param  string  $prefix
     * @return string
     */
    public static function start($value, $prefix)
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }

    /**
     * @param  string  $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * @param  string  $value
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @param  string  $title
     * @param  string  $separator
     * @param  string|null  $language
     * @return string
     */
    public static function slug($title, $separator = '-', $language = 'en')
    {
        $title = $language ? static::ascii($title, $language) : $title;

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator.'at'.$separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', static::lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string  $value
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * @param  string  $string
     * @param  int  $start
     * @param  int|null  $length
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * @param  string  $string
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(static::substr($string, 0, 1)).static::substr($string, 1);
    }

    /**
     * @param  callable  $factory
     * @return void
     */
    public static function createUuidsUsing(callable $factory = null)
    {
        static::$uuidFactory = $factory;
    }

    /**
     * @return void
     */
    public static function createUuidsNormally()
    {
        static::$uuidFactory = null;
    }

    /**
     * @param int|double|float $number
     * @param string $form1 ????????????????: ??????
     * @param string $form2 ????????????????: ????????
     * @param string|null $form3 ????????????????: ??????????
     * @return string
     */
    public static function declensionNumber($number, string $form1, string $form2, ?string $form3 = null): string
    {
        $number = abs($number) % 100;
        if ($number > 4 && $number < 21) return $form3 ? $form3 : $form2;

        $number = $number % 10;
        if ($number > 1 && $number < 5) return $form2;

        if ($number == 1) return $form1;

        return $form3 ? $form3 : $form2;
    }

    /**
     * @param int|double|float $number
     * @param string $form1 ????????????????: ??????????????
     * @param string $form2 ????????????????: ??????????????
     * @return string
     */
    public static function declensionAdjective($number, string $form1, string $form2): string
    {
        $number = abs($number) % 100;
        if ($number == 11) return $form2;

        $number %= 10;
        if ($number == 1) return $form1;

        return $form2;
    }

    /**
     * @param string $word
     * @param int $symbols
     * @param string $separator
     * @return string
     */
    public static function separateWord(string $word, int $symbols, string $separator = ' '): string
    {
        return implode($separator, str_split($word, $symbols));
    }

    /**
     * @return array
     */
    protected static function charsArray()
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = [
            '0'    => ['??', '???', '??', '???'],
            '1'    => ['??', '???', '??', '???'],
            '2'    => ['??', '???', '??', '???'],
            '3'    => ['??', '???', '??', '???'],
            '4'    => ['???', '???', '??', '??', '???'],
            '5'    => ['???', '???', '??', '??', '???'],
            '6'    => ['???', '???', '??', '??', '???'],
            '7'    => ['???', '???', '??', '???'],
            '8'    => ['???', '???', '??', '???'],
            '9'    => ['???', '???', '??', '???'],
            'a'    => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '???', '???', '???', '??', '??', '??', '???', '???', '??', '???', '??', '??'],
            'b'    => ['??', '??', '??', '???', '???', '???', '??'],
            'c'    => ['??', '??', '??', '??', '??', '???'],
            'd'    => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '???', '???', '???', '???', '??'],
            'e'    => ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '??', '??', '???'],
            'f'    => ['??', '??', '??', '??', '???', '???', '??', '??'],
            'g'    => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???', '??'],
            'h'    => ['??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '??'],
            'i'    => ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '???', '???', '??', '???', '???', '??', '??', '??', '???', '???', '???', '??????', '??', '???', '???', '??', '???', '??'],
            'j'    => ['??', '??', '??', '???', '??', '???'],
            'k'    => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '???', '??'],
            'l'    => ['??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??'],
            'm'    => ['??', '??', '??', '???', '???', '???', '??', '??'],
            'n'    => ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??'],
            'o'    => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??????', '??', '??', '??', '???', '???', '???', '??'],
            'p'    => ['??', '??', '???', '???', '??', '???', '??', '??'],
            'q'    => ['???', '???'],
            'r'    => ['??', '??', '??', '??', '??', '??', '???', '???', '??'],
            's'    => ['??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '???', '???', '??'],
            't'    => ['??', '??', '??', '??', '??', '??', '??', '???', '???', '??', '???', '???', '???', '??'],
            'u'    => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '??', '??'],
            'v'    => ['??', '???', '??', '???', '??'],
            'w'    => ['??', '??', '??', '???', '???', '???'],
            'x'    => ['??', '??', '???'],
            'y'    => ['??', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'z'    => ['??', '??', '??', '??', '??', '??', '???', '???', '???', '??'],
            'aa'   => ['??', '???', '??'],
            'ae'   => ['??', '??'],
            'ai'   => ['???'],
            'ch'   => ['??', '???', '???', '??'],
            'dj'   => ['??', '??'],
            'dz'   => ['??', '???', '????'],
            'ei'   => ['???'],
            'gh'   => ['??', '???'],
            'ii'   => ['???'],
            'ij'   => ['??'],
            'kh'   => ['??', '??', '???'],
            'lj'   => ['??'],
            'nj'   => ['??'],
            'oe'   => ['??', '??', '??'],
            'oi'   => ['???'],
            'oii'  => ['???'],
            'ps'   => ['??'],
            'sh'   => ['??', '???', '??', '??'],
            'shch' => ['??'],
            'ss'   => ['??'],
            'sx'   => ['??'],
            'th'   => ['??', '??', '??', '??', '??', '??'],
            'ts'   => ['??', '???', '???'],
            'ue'   => ['??'],
            'uu'   => ['???'],
            'ya'   => ['??'],
            'yu'   => ['??'],
            'zh'   => ['??', '???', '??'],
            '(c)'  => ['??'],
            'A'    => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '???', '??'],
            'B'    => ['??', '??', '???', '???'],
            'C'    => ['??', '??', '??', '??', '??', '???'],
            'D'    => ['??', '??', '??', '??', '??', '??', '???', '???', '??', '??', '???'],
            'E'    => ['??', '??', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '??', '???', '??', '??', '??', '??', '??', '???'],
            'F'    => ['??', '??', '???'],
            'G'    => ['??', '??', '??', '??', '??', '??', '???'],
            'H'    => ['??', '??', '??', '???'],
            'I'    => ['??', '??', '???', '??', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'J'    => ['???'],
            'K'    => ['??', '??', '???'],
            'L'    => ['??', '??', '??', '??', '??', '??', '??', '???', '???'],
            'M'    => ['??', '??', '???'],
            'N'    => ['??', '??', '??', '??', '??', '??', '??', '???'],
            'O'    => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???', '???', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '???', '??'],
            'P'    => ['??', '??', '???'],
            'Q'    => ['???'],
            'R'    => ['??', '??', '??', '??', '??', '???'],
            'S'    => ['??', '??', '??', '??', '??', '??', '??', '???'],
            'T'    => ['??', '??', '??', '??', '??', '??', '???'],
            'U'    => ['??', '??', '???', '??', '???', '??', '???', '???', '???', '???', '???', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '???', '??', '??'],
            'V'    => ['??', '???'],
            'W'    => ['??', '??', '??', '???'],
            'X'    => ['??', '??', '???'],
            'Y'    => ['??', '???', '???', '???', '???', '??', '???', '???', '???', '??', '??', '??', '??', '??', '??', '???'],
            'Z'    => ['??', '??', '??', '??', '??', '???'],
            'AE'   => ['??', '??'],
            'Ch'   => ['??'],
            'Dj'   => ['??'],
            'Dz'   => ['??'],
            'Gx'   => ['??'],
            'Hx'   => ['??'],
            'Ij'   => ['??'],
            'Jx'   => ['??'],
            'Kh'   => ['??'],
            'Lj'   => ['??'],
            'Nj'   => ['??'],
            'Oe'   => ['??'],
            'Ps'   => ['??'],
            'Sh'   => ['??', '??'],
            'Shch' => ['??'],
            'Ss'   => ['???'],
            'Th'   => ['??', '??', '??'],
            'Ts'   => ['??'],
            'Ya'   => ['??', '????'],
            'Yu'   => ['??', '????'],
            'Zh'   => ['??'],
            ' '    => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81", "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84", "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87", "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A", "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", "\xEF\xBE\xA0"],
        ];
    }

    /**
     * @param  string  $language
     * @return array|null
     */
    protected static function languageSpecificCharsArray($language)
    {
        static $languageSpecific;

        if (! isset($languageSpecific)) {
            $languageSpecific = [
                'bg' => [
                    ['??', '??', '??', '??', '??', '??', '??', '??'],
                    ['h', 'H', 'sht', 'SHT', 'a', '??', 'y', 'Y'],
                ],
                'da' => [
                    ['??', '??', '??', '??', '??', '??'],
                    ['ae', 'oe', 'aa', 'Ae', 'Oe', 'Aa'],
                ],
                'de' => [
                    ['??',  '??',  '??',  '??',  '??',  '??'],
                    ['ae', 'oe', 'ue', 'AE', 'OE', 'UE'],
                ],
                'he' => [
                    ['??', '??', '??', '??', '??', '??'],
                    ['??', '??', '??', '??', '??', '??'],
                    ['??', '??', '??', '??', '??', '??'],
                    ['??', '??', '??', '??', '??', '??', '??', '??', '??'],
                ],
                'ro' => [
                    ['??', '??', '??', '??', '??', '??', '??', '??', '??', '??'],
                    ['a', 'a', 'i', 's', 't', 'A', 'A', 'I', 'S', 'T'],
                ],
            ];
        }

        return $languageSpecific[$language] ?? null;
    }
}

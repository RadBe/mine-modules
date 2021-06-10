<?php

/*
 * This file is part of Respect/Validation.
 *
 * (c) Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

declare(strict_types=1);

namespace Respect\Validation\Exceptions;

/**
 * @author Alexandre Gomes Gaigalas <alexandre@gaigalas.net>
 * @author Danilo Correa <danilosilva87@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Mazen Touati <mazen_touati@hotmail.com>
 */
final class LengthException extends ValidationException
{
    public const BOTH = 'both';
    public const LOWER = 'lower';
    public const LOWER_INCLUSIVE = 'lower_inclusive';
    public const GREATER = 'greater';
    public const GREATER_INCLUSIVE = 'greater_inclusive';
    public const EXACT = 'exact';

    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::BOTH => '{{name}} должно иметь длину от {{minValue}} до {{maxValue}}',
            self::LOWER => '{{name}} должно иметь длину больше {{minValue}}',
            self::LOWER_INCLUSIVE => '{{name}} должно иметь длину больше или равную {{minValue}}',
            self::GREATER => '{{name}} должно иметь длину меньше {{maxValue}}',
            self::GREATER_INCLUSIVE => '{{name}} должно иметь длину меньше или равную {{maxValue}}',
            self::EXACT => '',
        ],
        self::MODE_NEGATIVE => [
            self::BOTH => '{{name}} не должно иметь длину от {{minValue}} до {{maxValue}}',
            self::LOWER => '{{name}} не должно иметь длину больше {{minValue}}',
            self::LOWER_INCLUSIVE => '{{name}} не должно иметь длину больше или равную {{minValue}}',
            self::GREATER => '{{name}} не должно иметь длину меньше {{maxValue}}',
            self::GREATER_INCLUSIVE => '{{name}} не должно иметь длину меньше или равную {{maxValue}}',
            self::EXACT => '{{name}} не должно иметь длину {{maxValue}}',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function chooseTemplate(): string
    {
        $isInclusive = $this->getParam('inclusive');

        if (!$this->getParam('minValue')) {
            return $isInclusive === true ? self::GREATER_INCLUSIVE : self::GREATER;
        }

        if (!$this->getParam('maxValue')) {
            return $isInclusive === true ? self::LOWER_INCLUSIVE : self::LOWER;
        }

        if ($this->getParam('minValue') == $this->getParam('maxValue')) {
            return self::EXACT;
        }

        return self::BOTH;
    }
}

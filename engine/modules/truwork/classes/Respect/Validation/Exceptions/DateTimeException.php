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
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class DateTimeException extends ValidationException
{
    public const FORMAT = 'format';

    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} должна быть действительной датой / временем',
            self::FORMAT => '{{name}} должно быть действительной датой / временем в формате {{sample}}',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} не должно быть действительной датой / временем',
            self::FORMAT => '{{name}} не должно быть действительной датой / временем в формате {{sample}}',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function chooseTemplate(): string
    {
        return $this->getParam('format') ? self::FORMAT : self::STANDARD;
    }
}

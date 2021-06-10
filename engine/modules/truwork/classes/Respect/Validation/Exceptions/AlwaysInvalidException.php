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
 * @author William Espindola <oi@williamespindola.com.br>
 */
final class AlwaysInvalidException extends ValidationException
{
    public const SIMPLE = 'simple';

    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} всегда недействителен',
            self::SIMPLE => '{{name}} недействителен',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} всегда действителен',
            self::SIMPLE => '{{name}} действительно',
        ],
    ];
}

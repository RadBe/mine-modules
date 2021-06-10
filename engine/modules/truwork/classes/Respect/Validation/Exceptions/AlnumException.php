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
final class AlnumException extends FilteredValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} должен содержать только буквы (a-z) и цифры (0-9)',
            self::EXTRA => '{{name}} должен содержать только буквы (a-z), цифры (0-9) и {{additionalChars}}',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} не должно содержать букв (a-z) или цифр (0-9)',
            self::EXTRA => '{{name}} не должно содержать букв (a-z), цифр (0-9) или {{additionalChars}}',
        ],
    ];
}

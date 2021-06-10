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
class AllOfException extends GroupedValidationException
{
    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::NONE => 'Все необходимые правила должны соответствовать {{name}}',
            self::SOME => 'Эти правила должны соответствовать {{name}}',
        ],
        self::MODE_NEGATIVE => [
            self::NONE => 'Ни одно из этих правил не должно соответствовать {{name}}',
            self::SOME => 'Эти правила не должны применяться для {{name}}',
        ],
    ];
}

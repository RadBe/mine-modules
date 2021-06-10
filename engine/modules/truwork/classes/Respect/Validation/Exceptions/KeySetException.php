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

use function count;

/**
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class KeySetException extends GroupedValidationException implements NonOmissibleException
{
    public const STRUCTURE = 'structure';

    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::NONE => 'Все необходимые правила должны соответствовать {{name}}',
            self::SOME => 'Эти правила должны соответствовать {{name}}',
            self::STRUCTURE => 'Должны быть ключи {{keys}}',
        ],
        self::MODE_NEGATIVE => [
            self::NONE => 'Ни одно из этих правил не должно соответствовать {{name}}',
            self::SOME => 'Эти правила не должны применяться для {{name}}',
            self::STRUCTURE => 'Не должно быть ключей {{keys}}',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function chooseTemplate(): string
    {
        if (count($this->getChildren()) === 0) {
            return self::STRUCTURE;
        }

        return parent::chooseTemplate();
    }
}

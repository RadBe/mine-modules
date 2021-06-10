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
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
final class KeyValueException extends ValidationException
{
    public const COMPONENT = 'component';

    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Ключ {{name}} должен присутствовать',
            self::COMPONENT => '{{baseKey}} должен быть действительным для проверки {{compareKey}}',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Ключ {{name}} не должен присутствовать',
            self::COMPONENT => '{{baseKey}} не должен быть действительным для проверки {{compareKey}}',
        ],
    ];

    protected function chooseTemplate(): string
    {
        return $this->getParam('component') ? self::COMPONENT : self::STANDARD;
    }
}

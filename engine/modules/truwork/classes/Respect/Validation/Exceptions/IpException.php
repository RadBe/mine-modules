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
 * @author Danilo Benevides <danilobenevides01@gmail.com>
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
final class IpException extends ValidationException
{
    public const NETWORK_RANGE = 'network_range';

    /**
     * {@inheritDoc}
     */
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} должен быть IP-адресом',
            self::NETWORK_RANGE => '{{name}} должен быть IP-адресом в диапазоне {{range}}',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} не должно быть IP-адресом',
            self::NETWORK_RANGE => '{{name}} не должно быть IP-адресом в диапазоне {{range}}',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function chooseTemplate(): string
    {
        if (!$this->getParam('range')) {
            return self::STANDARD;
        }

        return self::NETWORK_RANGE;
    }
}

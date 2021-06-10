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

namespace Respect\Validation\Rules;

use function is_string;
use function trim;

/**
 * Validates the given input with a defined rule when input is not EMPTY.
 *
 * @author Jens Segers <segers.jens@gmail.com>
 */
final class Emptyable extends AbstractWrapper
{
    /**
     * {@inheritDoc}
     */
    public function assert($input): void
    {
        if (is_string($input)) {
            $input = trim($input);
        }

        if (empty($input) && !is_numeric($input)) {
            return;
        }

        parent::assert($input);
    }

    /**
     * {@inheritDoc}
     */
    public function check($input): void
    {
        if (is_string($input)) {
            $input = trim($input);
        }

        if (empty($input) && !is_numeric($input)) {
            return;
        }

        parent::check($input);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($input): bool
    {
        if (is_string($input)) {
            $input = trim($input);
        }

        if (empty($input) && !is_numeric($input)) {
            return true;
        }

        return parent::validate($input);
    }
}

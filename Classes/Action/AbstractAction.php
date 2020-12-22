<?php

declare(strict_types=1);

/*
 * This file is part of the "Locate" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * Florian Wessels <f.wessels@Leuchtfeuer.com>, Leuchtfeuer Digital Marketing
 */

namespace Leuchtfeuer\Locate\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractAction implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $configuration = [];

    public function withConfiguration(array $configuration): self
    {
        $clonedObject = clone $this;
        $clonedObject->configuration = $configuration;

        return $clonedObject;
    }

    /**
     * Call the action module
     */
    abstract public function execute(): ?ResponseInterface;
}

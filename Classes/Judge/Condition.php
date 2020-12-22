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

namespace Leuchtfeuer\Locate\Judge;

use Leuchtfeuer\Locate\FactProvider\AbstractFactProvider;

class Condition extends AbstractJudge
{
    /**
     * @inheritDoc
     */
    public function adjudicate(AbstractFactProvider $factProvider, int $priority = AbstractJudge::DEFAULT_PRIORITY): AbstractJudge
    {
        $prosecution = $this->configuration['prosecution'] ?? $this->configuration['prosecution.'] ?? null;

        if ($prosecution !== null && $factProvider->isGuilty($prosecution)) {
            $this->decision = (new Decision())->withActionName($this->configuration['action']);
            $this->decision->setPriority($priority);

            if ($factProvider->isMultiple()) {
                $this->decision->setInternalPriority($factProvider->getPriority());
            }
        }

        return $this;
    }
}

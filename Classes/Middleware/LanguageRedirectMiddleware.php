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

namespace Leuchtfeuer\Locate\Middleware;

use Leuchtfeuer\Locate\Action\Redirect;
use Leuchtfeuer\Locate\Processor\Court;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LanguageRedirectMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$GLOBALS['TSFE']->tmpl instanceof TemplateService || empty($GLOBALS['TSFE']->tmpl->setup)) {
            $GLOBALS['TSFE']->forceTemplateParsing = true;
            $GLOBALS['TSFE']->getConfigArray();
        }

        $typoScript = $GLOBALS['TSFE']->tmpl->setup;

        if (isset($typoScript['plugin.']['tx_locate_pi1'])) {
            error_log('The TypoScript configuration was moved to "config.tx_locate"', E_DEPRECATED);
            $typoScript['config.']['tx_locate'] = $typoScript['plugin.']['tx_locate_pi1'];
            $typoScript['config.']['tx_locate.'] = array_merge_recursive($typoScript['config.']['tx_locate.'] ?? [], $typoScript['plugin.']['tx_locate_pi1.']);
        }

        if ((int)$typoScript['config.']['tx_locate'] === 1 && !empty($typoScript['config.']['tx_locate.'] ?? [])) {
            $locateSetup = $typoScript['config.']['tx_locate.'];

            $config = [
                'actions' => $locateSetup['actions.'] ?? [],
                'facts' => $locateSetup['facts.'] ?? [],
                'judges' => $locateSetup['judges.'] ?? [],
                'settings' => [
                    'dryRun' => isset($locateSetup['dryRun']) ? (bool)$locateSetup['dryRun'] : false,
                    'overrideParam' => $locateSetup['overrideParam'] ?? Redirect::OVERRIDE_PARAMETER,
                    'overrideCookie' => $locateSetup['overrideCookie'] ?? 0,
                    'cookieHandling' => $locateSetup['cookieHandling'] ?? 0,
                ],
            ];

            return GeneralUtility::makeInstance(Court::class, $config)->run() ?? $handler->handle($request);
        }

        return $handler->handle($request);
    }
}

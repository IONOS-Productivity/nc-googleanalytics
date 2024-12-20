<?php

// SPDX-FileCopyrightText: 2024 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\AppInfo;

use OC\Security\CSP\ContentSecurityPolicyManager;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IURLGenerator;
use OCP\Util;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'googleanalytics';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void
    {
    }

    public function boot(IBootContext $context): void
    {
        $context->injectFn([$this, 'addTrackingScript']);
        $context->injectFn([$this, 'addContentSecurityPolicy']);
    }

    public function addTrackingScript(IURLGenerator $urlGenerator, ContentSecurityPolicyNonceManager $nonceManager): void
    {
        Util::addHeader(
            'script',
            [
                'src' => $urlGenerator->linkToRoute('googleanalytics.JavaScript.tracking'),
                'nonce' => $nonceManager->getNonce(),
            ],
            ''
        );
    }

    /**
     * Add the Content Security Policy for the Google Analytics tracking according
     * to https://developers.google.com/tag-platform/security/guides/csp
     *
     * @param ContentSecurityPolicyManager $policyManager
     * @return void
     */
    public function addContentSecurityPolicy(ContentSecurityPolicyManager $policyManager): void
    {
        $policy = new ContentSecurityPolicy();

        $policy->addAllowedScriptDomain("*.googletagmanager.com");
        $policy->addAllowedImageDomain("*.googletagmanager.com");
        $policy->addAllowedConnectDomain("*.googletagmanager.com");

        $policy->addAllowedScriptDomain("tagmanager.google.com");
        $policy->addAllowedImageDomain("tagmanager.google.com");
        $policy->addAllowedConnectDomain("tagmanager.google.com");

        $policy->addAllowedScriptDomain("*.google-analytics.com");
        $policy->addAllowedImageDomain("*.google-analytics.com");
        $policy->addAllowedConnectDomain("*.google-analytics.com");

        // additional SCP for GTM preview mode
        $policy->addAllowedStyleDomain("https://www.googletagmanager.com");
        $policy->addAllowedStyleDomain("https://fonts.googleapis.com");

        $policy->addAllowedFontDomain("https://fonts.gstatic.com");

        $policy->addAllowedImageDomain("https://fonts.gstatic.com");
        $policy->addAllowedImageDomain("https://fonts.googleapis.com");

        $policyManager->addDefaultPolicy($policy);
    }
}

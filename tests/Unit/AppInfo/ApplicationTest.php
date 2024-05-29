<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\AppInfo;

use PHPUnit\Framework\TestCase;
use OC\Security\CSP\ContentSecurityPolicyManager;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCA\NCGoogleAnalytics\AppInfo\Application;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IURLGenerator;

class ApplicationTest extends TestCase
{
    private Application $application;
    private $urlGenerator;
    private $nonceManager;
    private $contentSecurityPolicyManager;
    private $context;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(IURLGenerator::class);
        $this->nonceManager = $this->createMock(ContentSecurityPolicyNonceManager::class);
        $this->contentSecurityPolicyManager = $this->createMock(ContentSecurityPolicyManager::class);
        $this->context = $this->createMock(IBootContext::class);
        $this->application = new Application();
    }

    public function testBoot(): void
    {
        $this->context->expects($this->exactly(2))
            ->method('injectFn')
            ->withConsecutive(
                [[$this->application, 'addTrackingScript']],
                [[$this->application, 'addContentSecurityPolicy']]
            );

        $this->application->boot($this->context);
    }
    public function testTrackingScriptAddition(): void
    {
        $this->urlGenerator->method('linkToRoute')->willReturn('someUrl');
        $this->nonceManager->method('getNonce')->willReturn('someNonce');

        $this->urlGenerator->expects($this->once())
            ->method('linkToRoute')
            ->with('googleanalytics.JavaScript.tracking');

        $this->nonceManager->expects($this->once())
            ->method('getNonce');

        // @todo Util::addHeader method call is not tested

        $this->application->addTrackingScript($this->urlGenerator, $this->nonceManager);
    }

    public function testContentSecurityPolicyAddition(): void
    {
        $allowedUrl = "*.googletagmanager.com tagmanager.google.com *.google-analytics.com";

        $policy = new ContentSecurityPolicy();

        $policy->addAllowedScriptDomain($allowedUrl);
        $policy->addAllowedImageDomain($allowedUrl);
        $policy->addAllowedConnectDomain($allowedUrl);

        $this->contentSecurityPolicyManager->expects($this->once())
            ->method('addDefaultPolicy')
            ->with($policy);

        $this->application->addContentSecurityPolicy($this->contentSecurityPolicyManager);
    }
}

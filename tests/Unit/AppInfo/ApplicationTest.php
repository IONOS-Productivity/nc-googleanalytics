<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\AppInfo;

use OC\Security\CSP\ContentSecurityPolicyManager;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCA\NCGoogleAnalytics\AppInfo\Application;
use OCA\NCGoogleAnalytics\Service\Consent\IConsentService;
use OCA\NCGoogleAnalytics\Service\Consent\IonosConsentService;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase {
	private Application $application;
	private IURLGenerator|MockObject $urlGenerator;
	private ContentSecurityPolicyNonceManager|MockObject $nonceManager;
	private ContentSecurityPolicyManager|MockObject $contentSecurityPolicyManager;
	private IBootContext|MockObject $context;
	private $registrationContext;

	protected function setUp(): void {
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
		$this->nonceManager = $this->createMock(ContentSecurityPolicyNonceManager::class);
		$this->contentSecurityPolicyManager = $this->createMock(ContentSecurityPolicyManager::class);
		$this->context = $this->createMock(IBootContext::class);
		$this->registrationContext = $this->createMock(IRegistrationContext::class);
		$this->application = new Application();
	}

	public function testBoot(): void {
		$this->context->expects($this->exactly(2))
			->method('injectFn')
			->withConsecutive(
				[[$this->application, 'addTrackingScript']],
				[[$this->application, 'addContentSecurityPolicy']]
			);

		$this->application->boot($this->context);
	}

	public function testRegister(): void {
		$this->registrationContext->expects($this->exactly(1))
			->method('registerService')
			->with(
				IConsentService::class,
				$this->isInstanceOf(\Closure::class),
				true
			);

		$this->application->register($this->registrationContext);
	}

	public function testConsentServiceRegistration(): void {
		$consentService = \OC::$server->getRegisteredAppContainer(Application::APP_ID)->get(IConsentService::class);

		$this->assertInstanceOf(IonosConsentService::class, $consentService, 'FATAL: IonosConsentService is not registered!');
	}

	public function testTrackingScriptAddition(): void {
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

	public function testContentSecurityPolicyAddition(): void {
		$policy = new ContentSecurityPolicy();

		$policy->addAllowedScriptDomain('*.googletagmanager.com');
		$policy->addAllowedImageDomain('*.googletagmanager.com');
		$policy->addAllowedConnectDomain('*.googletagmanager.com');

		$policy->addAllowedScriptDomain('tagmanager.google.com');
		$policy->addAllowedImageDomain('tagmanager.google.com');
		$policy->addAllowedConnectDomain('tagmanager.google.com');

		$policy->addAllowedScriptDomain('*.google-analytics.com');
		$policy->addAllowedImageDomain('*.google-analytics.com');
		$policy->addAllowedConnectDomain('*.google-analytics.com');

		$policy->addAllowedStyleDomain('https://www.googletagmanager.com');
		$policy->addAllowedStyleDomain('https://fonts.googleapis.com');

		$policy->addAllowedFontDomain('https://fonts.gstatic.com');

		$policy->addAllowedImageDomain('https://fonts.gstatic.com');
		$policy->addAllowedImageDomain('https://fonts.googleapis.com');

		$this->contentSecurityPolicyManager->expects($this->once())
			->method('addDefaultPolicy')
			->with($policy);

		$this->application->addContentSecurityPolicy($this->contentSecurityPolicyManager);
	}
}

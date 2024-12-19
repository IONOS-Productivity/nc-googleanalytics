<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\Unit\Controller;

use OCA\NCGoogleAnalytics\Config;
use OCA\NCGoogleAnalytics\Controller\JavaScriptController;
use OCA\NCGoogleAnalytics\Service\Consent\IConsent;
use OCA\NCGoogleAnalytics\Service\Consent\IConsentService;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\TextPlainResponse;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

/**
 * @backupGlobals enabled
 */
class JavaScriptControllerTest extends TestCase {
	private $controller;
	private $request;
	private $config;
	private IConsentService $consentServiceMock;

	protected function setUp(): void {
		$this->request = $this->createMock(IRequest::class);
		$this->config = $this->createMock(Config::class);
		$this->consentServiceMock = $this->createMock(IConsentService::class);
		$this->controller = new JavaScriptController('NCGoogleAnalytics', $this->request, $this->config, $this->consentServiceMock);
	}
	public function tearDown(): void {
		unset($_COOKIE['PRIVACY_CONSENT']);
	}

	public function testTrackingReturnsDisabledResponseWhenNoKey(): void {
		$this->config->method('getTrackingKey')->willReturn(null);

		$response = $this->controller->tracking();

		$this->assertInstanceOf(TextPlainResponse::class, $response);

		$this->assertArrayHasKey('Content-Type', $response->getHeaders());
		$this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
		$this->assertEquals('// tracking disabled', $response->render());
	}

	public function testValidTrackingKeyWithoutValidConsent() {
		$this->config->method('getTrackingKey')->willReturn('GTM-XXXXXX');

		$_COOKIE['PRIVACY_CONSENT'] = 'mocked_cookie_value';

		$this->consentServiceMock->expects($this->once())
			->method('getConsent')
			->with($_COOKIE)
			->willThrowException(new \InvalidArgumentException('mocked exception'));

		$response = $this->controller->tracking();

		$this->assertInstanceOf(TextPlainResponse::class, $response);

		$this->assertArrayHasKey('Content-Type', $response->getHeaders());
		$this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
		$this->assertEquals('// tracking disabled: no consent data', $response->render());
	}

	public function testValidTrackingKeyWithoutConsent() {
		$this->config->method('getTrackingKey')->willReturn('GTM-XXXXXX');

		$_COOKIE['PRIVACY_CONSENT'] = 'mocked_cookie_value';

		$this->consentServiceMock->expects($this->once())
			->method('getConsent')
			->with($_COOKIE)
			->willThrowException(new \Exception('mocked exception'));

		$response = $this->controller->tracking();

		$this->assertInstanceOf(TextPlainResponse::class, $response);

		$this->assertArrayHasKey('Content-Type', $response->getHeaders());
		$this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
		$this->assertEquals('// tracking disabled: no consent data', $response->render());
	}

	public function testTrackingReturnsScriptResponseWithNoConsentAndKeyExists(): void {
		$this->config->method('getTrackingKey')->willReturn('UA-123456-1');

		$_COOKIE['PRIVACY_CONSENT'] = 'mocked_cookie_value';

		$consentMock = $this->createMock(IConsent::class);
		$consentMock->expects($this->once())
			->method('hasStatisticsConsent')
			->willReturn(false);

		$consentMock->expects($this->never())->method('hasTechnicalConsent');
		$consentMock->expects($this->never())->method('hasMarketingConsent');
		$consentMock->expects($this->never())->method('hasPartnershipsConsent');

		$this->consentServiceMock->expects($this->once())
			->method('getConsent')
			->with($_COOKIE)
			->willReturn($consentMock);

		$response = $this->controller->tracking();

		$this->assertInstanceOf(TextPlainResponse::class, $response);

		$this->assertArrayHasKey('Content-Type', $response->getHeaders());
		$this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
		$this->assertEquals('// tracking disabled: no statistics consent', $response->render());
	}

	public function testTrackingReturnsScriptResponseWithConsentAndKeyExists(): void {
		$this->config->method('getTrackingKey')->willReturn('UA-123456-1');

		$_COOKIE['PRIVACY_CONSENT'] = 'mocked_cookie_value';

		$consentMock = $this->createMock(IConsent::class);
		$consentMock->expects($this->once())
			->method('hasStatisticsConsent')
			->willReturn(true);

		$consentMock->expects($this->never())->method('hasTechnicalConsent');
		$consentMock->expects($this->never())->method('hasMarketingConsent');
		$consentMock->expects($this->never())->method('hasPartnershipsConsent');

		$this->consentServiceMock->expects($this->once())
			->method('getConsent')
			->with($_COOKIE)
			->willReturn($consentMock);

		$response = $this->controller->tracking();

		$this->assertInstanceOf(DataDownloadResponse::class, $response);
		$this->assertArrayHasKey('Content-Type', $response->getHeaders());
		$this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
		$this->assertStringContainsString('UA-123456-1', $response->render());
	}
}

<?php

declare(strict_types=1);

// SPDX-FileCopyrightText: 2024 STRATO AG
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\Unit\Service\Consent;

use OCA\NCGoogleAnalytics\Service\Consent\IonosConsentService;
use PHPUnit\Framework\TestCase;

class IonosConsentServiceTest extends TestCase {
	public function testValidCookie() {
		$cookieValue = base64_encode(json_encode([
			'technical' => true,
			'statistics' => false,
			'marketing' => true,
			'partnerships' => false,
		]));

		$cookies = ['PRIVACY_CONSENT' => $cookieValue];
		$consent = (new IonosConsentService())->getConsent($cookies);

		$this->assertInstanceOf(\OCA\NCGoogleAnalytics\Service\Consent\IConsent::class, $consent);
		$this->assertTrue($consent->hasTechnicalConsent());
		$this->assertFalse($consent->hasStatisticsConsent());
		$this->assertTrue($consent->hasMarketingConsent());
		$this->assertFalse($consent->hasPartnershipsConsent());
	}

	public function testInvalidBase64Cookie() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid base64 string');

		$cookies = ['PRIVACY_CONSENT' => 'invalid_base64'];
		(new IonosConsentService())->getConsent($cookies);
	}

	public function testInvalidJsonCookie() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid JSON string');

		$cookieValue = base64_encode('invalid_json');
		$cookies = ['PRIVACY_CONSENT' => $cookieValue];
		(new IonosConsentService())->getConsent($cookies);
	}

	public function testMissingCookie() {
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('No consent cookie found');

		$cookies = [];
		$consent = (new IonosConsentService())->getConsent($cookies);

		$this->assertFalse($consent->hasTechnicalConsent());
		$this->assertFalse($consent->hasStatisticsConsent());
		$this->assertFalse($consent->hasMarketingConsent());
		$this->assertFalse($consent->hasPartnershipsConsent());
	}
}

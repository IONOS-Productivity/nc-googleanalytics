<?php

// SPDX-FileCopyrightText: 2024 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\Service\Consent;

class IonosConsentService implements IConsentService {
	private const COOKIE_NAME = 'PRIVACY_CONSENT';

	public function getConsent(array $cookies): IConsent {
		$encodedValue = $cookies[self::COOKIE_NAME] ?? '';

		if (empty($encodedValue)) {
			throw new \InvalidArgumentException('No consent cookie found');
		}

		$decoded = base64_decode($encodedValue, true);
		if ($decoded === false) {
			throw new \InvalidArgumentException('Invalid base64 string');
		}

		$data = json_decode($decoded, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException('Invalid JSON string');
		}

		return new Consent(
			$data[ConsentCategory::TECHNICAL->value] ?? false,
			$data[ConsentCategory::STATISTICS->value] ?? false,
			$data[ConsentCategory::MARKETING->value] ?? false,
			$data[ConsentCategory::PARTNERSHIPS->value] ?? false
		);
	}
}

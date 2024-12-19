<?php

// SPDX-FileCopyrightText: 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\Service\Consent;

class Consent implements IConsent {
	public function __construct(
		private readonly bool $technical = false,
		private readonly bool $statistics = false,
		private readonly bool $marketing = false,
		private readonly bool $partnerships = false,
	) {
	}

	public function hasTechnicalConsent(): bool {
		return $this->technical;
	}

	public function hasStatisticsConsent(): bool {
		return $this->statistics;
	}

	public function hasMarketingConsent(): bool {
		return $this->marketing;
	}

	public function hasPartnershipsConsent(): bool {
		return $this->partnerships;
	}
}

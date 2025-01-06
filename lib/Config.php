<?php

// SPDX-FileCopyrightText: 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics;

use OCP\IConfig;

class Config {
	public function __construct(
		protected string $appName,
		protected IConfig $config,
	) {
	}

	public function getTrackingKey(): ?string {
		$value = $this->config->getSystemValueString('googleanalytics_tracking_key');
		return (empty($value)) ? null : $value;
	}
}

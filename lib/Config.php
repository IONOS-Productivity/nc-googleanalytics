<?php

// SPDX-FileCopyrightText: 2024 2024 STRATO AG
//
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics;

use OCP\IConfig;

class Config
{
    public function __construct($appName, IConfig $config)
    {
        $this->appName = $appName;
        $this->config = $config;
    }

    public function getTrackingKey(): ?string
    {
        $value = $this->config->getSystemValueString('googleanalytics_tracking_key', '');
        return (empty($value)) ? null : $value;
    }
}

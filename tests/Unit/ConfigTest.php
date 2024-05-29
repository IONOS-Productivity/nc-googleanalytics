<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\Unit;

use OCA\NCGoogleAnalytics\Config;
use OCP\IConfig;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $config;
    private $appName;
    private $systemConfig;

    protected function setUp(): void
    {
        $this->appName = 'googleanalytics';
        $this->systemConfig = $this->createMock(IConfig::class);
        $this->config = new Config($this->appName, $this->systemConfig);
    }

    public function testTrackingKeyRetrievalWithExistingKey(): void
    {
        $this->systemConfig->method('getSystemValueString')
            ->with('googleanalytics_tracking_key', '')
            ->willReturn('UA-123456-1');

        $this->assertEquals('UA-123456-1', $this->config->getTrackingKey());
    }

    public function testTrackingKeyRetrievalWithNoKey(): void
    {
        $this->systemConfig->method('getSystemValueString')
            ->with('googleanalytics_tracking_key', '')
            ->willReturn('');

        $this->assertNull($this->config->getTrackingKey());
    }
}

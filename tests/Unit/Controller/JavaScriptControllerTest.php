<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\Unit\Controller;

use OCA\NCGoogleAnalytics\Config;
use OCA\NCGoogleAnalytics\Controller\JavaScriptController;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\TextPlainResponse;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

class JavaScriptControllerTest extends TestCase
{
    private $controller;
    private $request;
    private $config;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(Config::class);
        $this->controller = new JavaScriptController('NCGoogleAnalytics', $this->request, $this->config);
    }

    public function testTrackingReturnsDisabledResponseWhenNoKey(): void
    {
        $this->config->method('getTrackingKey')->willReturn(null);

        $response = $this->controller->tracking();

        $this->assertInstanceOf(TextPlainResponse::class, $response);

        $this->assertArrayHasKey('Content-Type', $response->getHeaders());
        $this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
        $this->assertEquals('// tracking disabled', $response->render());
    }

    public function testTrackingReturnsScriptResponseWhenKeyExists(): void
    {
        $this->config->method('getTrackingKey')->willReturn('UA-123456-1');

        $response = $this->controller->tracking();

        $this->assertInstanceOf(DataDownloadResponse::class, $response);
        $this->assertArrayHasKey('Content-Type', $response->getHeaders());
        $this->assertEquals('text/javascript', $response->getHeaders()['Content-Type']);
        $this->assertStringContainsString('UA-123456-1', $response->render());
    }
}

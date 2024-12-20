<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Mikhailo Matiyenko-Kupriyanov <kupriyanov+nextcloud@strato.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NCGoogleAnalytics\Tests\Integration\Controller;

use Exception;
use OCA\NCGoogleAnalytics\Controller\JavaScriptController;
use OCP\AppFramework\App;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\TextPlainResponse;
use OCP\HintException;
use OCP\IConfig;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class GoogleAnalyticsTest extends TestCase {
	private JavaScriptController $controller;

	private IConfig $config;

	/**
	 * @throws ContainerExceptionInterface
	 * @throws HintException
	 * @throws NotFoundExceptionInterface
	 */
	public function setUp(): void {
		$this->config = \OC::$server->getConfig();
		$this->config->setSystemValue('googleanalytics_tracking_key', null);

		$app = new App('googleanalytics');
		$container = $app->getContainer();

		$this->controller = $container->get(JavaScriptController::class);
	}

	public function tearDown(): void {
		$this->config->setSystemValue('googleanalytics_tracking_key', null);
	}

	/**
	 * @throws Exception
	 */
	public function testTrackingReturnsDisabledResponseWhenNoKey(): void {
		$response = $this->controller->tracking();

		$this->assertInstanceOf(TextPlainResponse::class, $response);
		$this->assertEquals('// tracking disabled', $response->render());
	}

	/**
	 * @throws Exception
	 */
	public function testTrackingReturnsScriptResponseWhenKeyExists(): void {
		$this->config->setSystemValue('googleanalytics_tracking_key', 'UA-123456-1');

		$response = $this->controller->tracking();

		$this->assertInstanceOf(DataDownloadResponse::class, $response);
		$this->assertStringContainsString('UA-123456-1', $response->render());
	}
}

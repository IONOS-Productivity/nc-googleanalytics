<?php
declare(strict_types=1);

namespace OCA\EncAnalytics\AppInfo;

use OC\Security\CSP\ContentSecurityPolicyManager;
use OC\Security\CSP\ContentSecurityPolicyNonceManager;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IURLGenerator;
use OCP\Util;

class Application extends App implements IBootstrap {
	public const APP_ID = 'enc_analytics';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
	}

	public function boot(IBootContext $context): void {
		$context->injectFn([$this, 'addTrackingScript']);
		$context->injectFn([$this, 'addContentSecurityPolicy']);
	}

	public function addTrackingScript(IURLGenerator $urlGenerator, ContentSecurityPolicyNonceManager $nonceManager): void {
		Util::addHeader(
			'script',
			[
				'src' => $urlGenerator->linkToRoute('enc_analytics.JavaScript.tracking'),
				'nonce' => $nonceManager->getNonce(),
			], ''
		);
	}

	public function addContentSecurityPolicy(ContentSecurityPolicyManager $policyManager): void {
		$allowedUrl = "www.googletagmanager.com";
		$policy = new ContentSecurityPolicy();

		$policy->addAllowedScriptDomain($allowedUrl);
		$policy->addAllowedImageDomain($allowedUrl);
		$policy->addAllowedConnectDomain($allowedUrl);

		$policyManager->addDefaultPolicy($policy);
	}
}

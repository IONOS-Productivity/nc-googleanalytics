<?php

declare(strict_types=1);

namespace OCA\NCGoogleAnalytics\AppInfo;

use OCA\NCGoogleAnalytics\Listener\LoadScript;
use OCA\NCGoogleAnalytics\Listener\AddCsp;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'googleanalytics';

    public function __construct()
    {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerEventListener(BeforeTemplateRenderedEvent::class, LoadScript::class);
        $context->registerEventListener(AddContentSecurityPolicyEvent::class, AddCsp::class);
    }

    public function boot(IBootContext $context): void
    {
    }
}

<?php

namespace App\Infrastructure\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 100)]
class CorsAccessControlListener
{
    private array $blacklistedOrigins;
    private array $whitelistedOrigins;

    public function __construct(
        #[Autowire('%env(csv:CORS_BLACKLIST)%')] array $blacklistedOrigins = [],
        #[Autowire('%env(csv:CORS_WHITELIST)%')] array $whitelistedOrigins = []
    ) {
        $this->blacklistedOrigins = $blacklistedOrigins;
        $this->whitelistedOrigins = $whitelistedOrigins;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $origin = $request->headers->get('Origin');

        // If no Origin header, usually same-origin or non-browser, let it pass or block depending on policy.
        // For API, we mostly care if Origin is present.
        if (!$origin) {
            return;
        }

        // 1. Check Blacklist
        if ($this->isListed($origin, $this->blacklistedOrigins)) {
            $event->setResponse(new Response('Origin is blacklisted.', 403));
            return;
        }

        // 2. Check Whitelist (if defined and not empty, strict mode)
        // If whitelist is empty, we assume openness (or handled by Nelmio).
        // But user asked for "White list implementation".
        if (!empty($this->whitelistedOrigins)) {
            if (!$this->isListed($origin, $this->whitelistedOrigins)) {
                $event->setResponse(new Response('Origin is not whitelisted.', 403));
            }
        }
    }

    private function isListed(string $origin, array $list): bool
    {
        foreach ($list as $pattern) {
            // Simple wildcard matching or exact match
            if ($pattern === '*' || $pattern === $origin) {
                return true;
            }
            // Optional: Regex support
            // if (preg_match('#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#', $origin)) {
            //     return true;
            // }
        }
        return false;
    }
}

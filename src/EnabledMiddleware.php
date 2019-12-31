<?php

namespace DaveJamesMiller\RouteBrowser;

use Closure;

class EnabledMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$this->enabled()) {
            abort(403);
        }

        return $next($request);
    }

    private function enabled(): bool
    {
        $enabled = config('route-browser.enabled');

        if ($enabled === null) {
            // Note: Don't use app()->isLocal() or app()->environment() because
            // that doesn't re-read the config which makes testing harder
            $enabled = config('app.debug') && config('app.env') === 'local';
        }

        return $enabled;
    }
}

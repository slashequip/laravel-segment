<?php

namespace SlashEquip\LaravelSegment\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SlashEquip\LaravelSegment\Contracts\CanBeIdentifiedForSegment;
use SlashEquip\LaravelSegment\Facades\Segment;

class ApplySegmentGlobals
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        /**
         * Set the current logged in User as global.
         */
        if ($user = Auth::guard($guard)->user()) {
            /** @var CanBeIdentifiedForSegment $user */
            Segment::setGlobalUser($user);
        }

        /**
         * Build some nice default context based on the current request.
         */
        Segment::setGlobalContext($this->getContext($request));
        
        return $next($request);
    }

    private function getContext(Request $request): array
    {
        return collect([
                "ip" => $request->ip(),
                "locale" => $request->getPreferredLanguage(),
                "userAgent" => $request->userAgent(),

                /**
                 * This is a solid default, generally backend calls
                 * to Segment are not responsible to determining
                 * whether or a not a user is active or not.
                 */
                "active" => false,
            ])
            ->filter(function ($context) {
                // Top level null values in the context
                // are meaningless at this point.
                return ! is_null($context);
            })
            ->all();
    }
}

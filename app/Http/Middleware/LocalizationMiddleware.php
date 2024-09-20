<?php

namespace App\Http\Middleware;

use App\Enums\Language;
use App\Traits\HasHiddenValidation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    use HasHiddenValidation;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $language = 'en';

        $user = $request->user();

        if (! $user) {
            $this->validateHidden([
                'language' => ['required', 'string', new Enum(Language::class)],
            ]);
            $language = $request->language;
        } else {
            $language = $user->language->value;
        }

        app()->setLocale($language);

        return $next($request);
    }
}

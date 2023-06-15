<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //檢查 Session 中有沒有 Locale 有的話就用，沒有的話就用 HTTP_ACCEPT_LANGUAGE 去抓使用者的語言
        if (Session::has('locale')) {
            $lang = Session::get('locale', Config::get('app.locale'));
        } else {
            $browser_lang = strtolower(explode(',', request()->server('HTTP_ACCEPT_LANGUAGE'))[0]);
            $lang = (!array_key_exists($browser_lang, config('app.supported_locales'))) ? config('app.fallback_locale') : $browser_lang;
            Session::put('locale', $lang);
        }
        App::setLocale($lang);
        return $next($request);
    }
}

<?php 

namespace Ceb\Http\Middleware;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Closure;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Session\Store as Session;


class LocaleMiddleware {


   public function handle($request, Closure $next)
   {
        if(Sentry::check()){
            app()->setLocale(Sentry::getUser()->language);
        }

        return $next($request);
     }

 }
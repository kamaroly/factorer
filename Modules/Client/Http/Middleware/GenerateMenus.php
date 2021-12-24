<?php

namespace Modules\Client\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GenerateMenus
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

        /*
         *
         * Module Menu for Admin Backend
         *
         * *********************************************************************
         */
        \Menu::make('admin_sidebar', function ($menu) {

            // Clients
            $menu->add('<i class="fas fa-users c-sidebar-nav-icon"></i> Clients', [
                'route' => 'backend.clients.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 84,
                'activematches' => ['admin/client*'],
                'permission'    => ['view_Clients'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}

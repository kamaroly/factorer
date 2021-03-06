<?php

namespace Modules\Order\Http\Middleware;

use Closure;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         *
         * Module Menu for Admin Backend
         *
         * *********************************************************************
         */
        \Menu::make('admin_sidebar', function ($menu) {

            // comments
            $menu->add('<i class="fas fa-box c-sidebar-nav-icon"></i> Commandes', [
                'route' => 'backend.order.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 85,
                'activematches' => ['admin/client*'],
                'permission'    => ['view_client'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}

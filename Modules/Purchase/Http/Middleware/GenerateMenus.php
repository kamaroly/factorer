<?php

namespace Modules\Purchase\Http\Middleware;

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

            // Purchasess
            $menu->add('<i class="fas fa-money-bill c-sidebar-nav-icon"></i> Purchases', [
                'route' => 'backend.purchase.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 84,
                'activematches' => ['admin/Purchases*'],
                'permission'    => ['view_purchases'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}

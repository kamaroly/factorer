<?php

namespace Modules\Billing\Http\Middleware;

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

            // billings
            $menu->add('<i class="fas fa-money-bill c-sidebar-nav-icon"></i> Facturations', [
                'route' => 'backend.billing.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 84,
                'activematches' => ['admin/billings*'],
                'permission'    => ['view_billings'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}

<?php

namespace Modules\Receivings\Http\Middleware;

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
            $menu->add('<i class="fas fa-box c-sidebar-nav-icon"></i> Stock matiere premier', [
                'route' => 'backend.receivings.index',
                'class' => 'c-sidebar-nav-item',
            ])
            ->data([
                'order'         => 85,
                'activematches' => ['admin/receivings*'],
                'permission'    => ['view_receivings'],
            ])
            ->link->attr([
                'class' => 'c-sidebar-nav-link',
            ]);
        })->sortBy('order');

        return $next($request);
    }
}

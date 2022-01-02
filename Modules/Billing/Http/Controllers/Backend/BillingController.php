<?php

namespace Modules\Billing\Http\Controllers\Backend;

use App\Authorizable;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;


class BillingController extends Controller
{

    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Bills';

        // module name
        $this->module_name = 'billing';

        // module icon
        $this->module_icon = 'fas fa-money-bill';

        // module model name, path
        $this->module_model = "Modules\Billing\Entities\Bill";
    }

    /**
     * Welcome Page for Billing Module
     *
     * @return Renderable
     */
    public function index(){


        return view(
            "billing::backend.{$this->module_name}.index_datatable",
            ['module_title' => $this->module_title,
             'module_name' => $this->module_name,
             'module_icon' => $this->module_icon,
             'module_name_singular' => Str::singular($this->module_name),
             'module_action' => 'List',
             ]
        );
    }
}
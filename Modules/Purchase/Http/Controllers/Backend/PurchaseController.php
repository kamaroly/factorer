<?php

namespace Modules\Purchase\Http\Controllers\Backend;

use App\Authorizable;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;


class PurchaseController extends Controller
{

    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Purchases';

        // module name
        $this->module_name = 'purchase';

        // module icon
        $this->module_icon = 'fas fa-money-bill';

        // module model name, path
        $this->module_model = "Modules\Purchase\Entities\Bill";
    }

    /**
     * Welcome Page for Purchase Module
     *
     * @return Renderable
     */
    public function index(){


        return view(
            "purchase::backend.{$this->module_name}s.index_datatable",
            ['module_title' => $this->module_title,
             'module_name' => $this->module_name,
             'module_icon' => $this->module_icon,
             'module_name_singular' => Str::singular($this->module_name),
             'module_action' => 'List',
             ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view(
            "purchase::backend.{$this->module_name}s.create",
             [
                'module_title' => $this->module_title,
                'module_name' => $this->module_name,
                'module_icon' => $this->module_icon,
                'module_name_singular' => Str::singular($this->module_name),
                'module_action' => 'Create',
             ]        
        );
    }
}
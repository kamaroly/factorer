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
        $this->module_title = 'Receivings';

        // module name
        $this->module_name = 'Receivings';

        // module icon
        $this->module_icon = 'fas fa-money-bill';

        // module model name, path
        $this->module_model = "Modules\Receivings\Entities\Bill";
    }

    /**
     * Welcome Page for Purchases Module
     *
     * @return Renderable
     */
    public function index(){

        return view(
            "receivings::backend.{$this->module_name}.index_datatable",
            [
                'module_title' => $this->module_title,
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
            "receivings::backend.{$this->module_name}.create",
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
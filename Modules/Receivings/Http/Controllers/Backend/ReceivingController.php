<?php

namespace Modules\Receivings\Http\Controllers\Backend;

use App\Authorizable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class ReceivingController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Receiving';

        // module name
        $this->module_name = 'receivings';

        // module icon
        $this->module_icon = 'fas fa-cart';

        // module model name, path
        $this->module_model = "Modules\Receivings\Entities\Bill";
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view(
            "receivings::backend.{$this->module_name}.index_datatable",
            ['module_title' => $this->module_title,
             'module_name' => $this->module_name,
             'module_icon' => $this->module_icon,
             'module_name_singular' => Str::singular($this->module_name),
             'module_action' => 'List',
             ]
        );
    }

    /**
     * Show form for creating new receiving
     * 
     * @return Response
     */
    public function create()
    {

        return view(
                "receivings::backend.$this->module_name.create",
                [
                    'module_title' => $this->module_title, 
                'module_name' => $this->module_name, 
                'module_icon' => $this->module_icon, 
                'module_action' => 'Create', 
                'module_name_singular' => Str::singular($this->module_name),
                ]);
    }
}

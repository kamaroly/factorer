<?php

namespace Modules\Client\Http\Controllers\Backend;

use App\Authorizable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;

class ClientController extends Controller
{

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Client';

        // module name
        $this->module_name = 'client';

        // directory path of the module
        $this->module_path = 'client';

        // module icon
        $this->module_icon = 'fas fa-sitemap';

    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view(
            "client::backend.{$this->module_name}.index_datatable",
            ['module_title' => $this->module_title,
             'module_name' => $this->module_name,
             'module_icon' => $this->module_icon,
             'module_name_singular' => Str::singular($this->module_name),
             'module_action' => 'List',
             ]
        );
    }
}

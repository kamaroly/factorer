<?php

namespace Modules\Receivings\Http\Controllers\Backend;

use App\Authorizable;
use Laracasts\Flash\Flash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Receivings\Http\Requests\ReceivingRequest;

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
        $this->module_model = "Modules\Receivings\Entities\Receiving";
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

    
    public function index_data()
    {
        $module_name = $this->module_name;
        $module_model = $this->module_model;

        $module_action = 'List';
        
        $$module_name = $module_model::select('id', 'item_name', 'item_sku', 'item_qty', 'item_buying_price', 'item_selling_price', 'grouping', "updated_at", "received_at");

        $data = $$module_name;

        return Datatables::of($$module_name)
                        ->addColumn('action', function ($data) {
                            $module_name = $this->module_name;

                            return view('backend.includes.action_column', compact('module_name', 'data'));
                        })
                        ->editColumn('item_name', function ($data) {
                            $is_featured = ($data->is_featured) ? '<span class="badge badge-primary">Featured</span>' : '';

                            return $data->name.' '.$data->status_formatted.' '.$is_featured;
                        })
                        ->editColumn('updated_at', function ($data) {
                            $module_name = $this->module_name;

                            $diff = Carbon::now()->diffInHours($data->updated_at);
                            if ($diff < 25) {
                                return $data->updated_at->diffForHumans();
                            } else {
                                return $data->updated_at->isoFormat('LLLL');
                            }
                        })
                        ->rawColumns(['item_name', 'item_sku', 'item_qty', 'item_buying_price', 'item_selling_price', 'grouping'])
                        ->orderColumns(['id'], '-:column $1')
                        ->make(true);
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

    public function store(ReceivingRequest $request)
    {
        $this->module_model::create($request->except("_token"));

        Flash::success("<i class='fas fa-check'></i> New '".Str::singular($this->module_title)."' Added")->important();

        return redirect("admin/$this->module_name");
    }
}

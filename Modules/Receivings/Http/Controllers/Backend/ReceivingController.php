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
        $this->module_title = 'Stock matiere premiere';

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
        
        $$module_name = $module_model::select('id', 'item_name', 'item_qty','item_type' ,'item_buying_price', 'item_total', 'item_total',"updated_at", "received_at");


        return Datatables::of($$module_name)
                        ->addColumn('action', function ($data) {
                            $module_name = $this->module_name;

                            return view('backend.includes.action_column', compact('module_name', 'data'));
                        })
                        ->editColumn('updated_at', function ($data) {
                            $module_name = $this->module_name;

                           /// $diff = Carbon::now()->diffInHours($data->created_at);
                             return $data->updated_at->format('Y-m-d');
                        })
                        ->rawColumns(['item_name', 'item_qty', 'item_type','item_buying_price', 'item_total', 'item_comment','updated_at'])
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

    /**
     * Store data in Database
     *
     * @param ReceivingRequest $request
     * @return Redirect
     */
    public function store(ReceivingRequest $request)
    {
        $this->module_model::create($request->except("_token"));

        Flash::success("<i class='fas fa-check'></i> New '".Str::singular($this->module_title)."' Added")->important();

        return redirect("admin/$this->module_name");
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $module_name_singular = Str::singular($this->module_name);
        $model = $this->module_model::findOrFail($id);

        return view(
            "receivings::backend.$this->module_name.edit",
                [
                    'module_title' => $this->module_title, 
                    'module_name' => $this->module_name, 
                    'module_icon' => $this->module_icon, 
                    'module_action' => 'Edit', 
                    'module_name_singular' => $module_name_singular,
                    'model' => $model
                ]
        );
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(ReceivingRequest $request, $id)
    {

        $this->module_model::findOrFail($id)->update($request->except("_token"));

        Flash::success("<i class='fas fa-check'></i> '".Str::singular($this->module_title)."' Updated Successfully")->important();

        return redirect("admin/$this->module_name");
    }



        /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function show($id)
    {

         $module_name_singular = Str::singular($this->module_name);
        $model = $this->module_model::findOrFail($id);

        return view(
            "receivings::backend.$this->module_name.edit",
                [
                    'module_title' => $this->module_title, 
                    'module_name' => $this->module_name, 
                    'module_icon' => $this->module_icon, 
                    'module_action' => 'Show', 
                    'module_name_singular' => $module_name_singular,
                    'model' => $model
                ]
        );
    }
}

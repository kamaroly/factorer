<?php

namespace Modules\Client\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Authorizable;
use Laracasts\Flash\Flash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Modules\Client\Http\Requests\ClientRequest;

class ClientController extends Controller
{

     public function __construct()
    {
        // Page Title
        $this->module_title = 'Client';

        // module name
        $this->module_name = 'client';

        // module icon
        $this->module_icon = 'fas fa-cart';

        // module model name, path
        $this->module_model = "Modules\Client\Entities\Client";
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

     public function index_data()
    {
        $module_name = $this->module_name;
        $module_model = $this->module_model;
        
        $$module_name = $module_model::select('id', 'client_id', 'first_name', 'last_name', 'company_name','TIN','telephone', "created_at","updated_at");


        return Datatables::of($$module_name)
                        ->addColumn('action', function ($data) {
                            $module_name = $this->module_name;

                            return view('backend.includes.action_column', compact('module_name', 'data'));
                        })
                        ->editColumn('created_at', function ($data) {
                            $module_name = $this->module_name;

                           /// $diff = Carbon::now()->diffInHours($data->created_at);
                             return $data->created_at->format('Y-m-d');
                              /// $this->created_at = Carbon::now()->format('Y-m-d H:i:s');

                        
                        })
                        ->rawColumns(['id','client_id', 'first_name', 'last_name', 'company_name', 'TIN', 'telephone','created_at'])
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
                "client::backend.$this->module_name.create",
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
    public function store(ClientRequest $request)
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
            "client::backend.$this->module_name.edit",
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
    public function update(ClientRequest $request, $id)
    {

        $this->module_model::findOrFail($id)->update($request->except("_token"));

        Flash::success("<i class='fas fa-check'></i> '".Str::singular($this->module_title)."' Updated Successfully")->important();

        return redirect("admin/$this->module_name");
    }
}

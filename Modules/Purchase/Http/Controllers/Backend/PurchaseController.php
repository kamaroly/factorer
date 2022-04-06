<?php

namespace Modules\Purchase\Http\Controllers\Backend;

use App\Authorizable;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Modules\Purchase\Http\Requests\PurchaseRequest;
use Modules\Purchase\Entities\Purchase;
use App\Models\User;




class PurchaseController extends Controller
{

    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Purchase';

        // module name
        $this->module_name = 'purchase';

        // module icon
        $this->module_icon = 'fas fa-money-bill';

        // module model name, path
        $this->module_model = "Modules\Purchase\Entities\Purchase";
    }

      /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {   
        return view(
            "purchase::backend.{$this->module_name}.index_datatable",
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
         
         $$module_name = $module_model::
              join('users', 'users.id', '=', 'purchases.userid')
             ->select('purchases.id', 'item_name', 'item_qty','item_type' ,'item_mouvement','approved_by', 'item_comment','users.name',"purchases.updated_at", "purchases.created_at");


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
                        ->rawColumns(['item_name', 'item_qty', 'item_type','item_comment','item_movement',
                            'updated_at'])
                        ->orderColumns(['purchases.id'], '-:column $1')
                        ->make(true);

                             
    }

        /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view(
            "purchase::backend.{$this->module_name}.create",
             [
                'module_title' => $this->module_title,
                'module_name' => $this->module_name,
                'module_icon' => $this->module_icon,
                'module_name_singular' => Str::singular($this->module_name),
                'module_action' => 'Create',
             ]        
        );
    }
   /**
     * Store data in Database
     *
     * @param Request $request
     * @return Redirect
     */
    public function store(PurchaseRequest $request)
    {    
         $attributes = $request->except("_token");
         $attributes['userid'] = request()->user()->id;

         $this->module_model::create($attributes);

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
            "purchase::backend.$this->module_name.edit",
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
    public function update(PurchaseRequest $request, $id)
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
            "purchase::backend.$this->module_name.edit",
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

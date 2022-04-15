<?php

namespace Modules\Accounting\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Modules\Accounting\Entities\Accounting;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\Posting;

class AccountingController extends Controller
{    

      public function __construct()
    {
        // Page Title
        $this->module_title = 'Accounting';

        // module name
        $this->module_name = 'accounting';

        // module icon
        $this->module_icon = 'fas fa-money-bill';

        // module model name, path
        $this->module_model = "Modules\Accounting\Entities\Accounting";
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {    
        return view(
            "accounting::backend.{$this->module_name}.index_datatable",
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
        

          $$module_name = Posting::get();

   //dd(Posting::get());

        return Datatables::of($$module_name)
                        ->addColumn('action', function ($data) {
                            $module_name = $this->module_name;

                            return view('backend.includes.action_column', compact('module_name', 'data'));
                        })
                         ->addColumn('debit', function ($data) {
                           return $data->debit->entitled;
                        })
                           ->addColumn('credit', function ($data) {
                           return $data->credit->entitled;
                        })
                        ->editColumn('created_at', function ($data) {
                            $module_name = $this->module_name;

                           /// $diff = Carbon::now()->diffInHours($data->created_at);
                             return $data->created_at->format('Y-m-d');
                        })
                        ->rawColumns(['id','debit_account_id','entitled','amount','note','description','created_at'])
                        
                        ->make(true);

                             
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view(
            "accounting::backend.{$this->module_name}.create",
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
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('accounting::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('accounting::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
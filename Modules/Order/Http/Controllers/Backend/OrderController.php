<?php

namespace Modules\Order\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Laracasts\Flash\Flash;
use Modules\Order\Entities\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'Order';

        // module name
        $this->module_name = 'order';

        // module icon
        $this->module_icon = 'fas fa-cart';

        // module model name, path
        $this->module_model = "Modules\Order\Entities\Order";
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('order::index',  
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
     * @return Renderable
     */
    public function create()
    {
        return view('order::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $orderAttributes = $request->except('_token');
        $order  = [];

        $orderTransactionId = now()->timestamp;
        
        for($i=0; $i < count($orderAttributes['id']); $i++){

            $totalPrice = $orderAttributes['price'][$i] * $orderAttributes['quantity'][$i];

            $order[] = [
                "order_transaction_id" => $orderTransactionId,
                "item_id"              => $orderAttributes['id'][$i],
                "item_name"            => $orderAttributes['name'][$i],
                "quantity"             => $orderAttributes['quantity'][$i],
                "unit_price"           => $orderAttributes['price'][$i],
                "total_price"          => $totalPrice,
            ];
        }

       if( Order::insert($order)){
        Flash::success("<i class='fas fa-check'></i> New '".Str::singular($this->module_title)."' Added")->important();
       }
        

        return $this->index();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('order::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('order::edit');
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

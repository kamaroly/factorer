<?php

namespace Modules\Order\Http\Controllers\Backend;

use Modules\Accounting\Repositories\AccountingRepository;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Support\Renderable;
use App\Notifications\NewOrderNotification;
use Modules\Accounting\Entities\Posting;
use Modules\Purchase\Entities\Purchase;
use Modules\Client\Entities\Client;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use App\Models\User;


class OrderController extends Controller
{
    public function __construct(AccountingRepository $accountingRepo)
    {
        // Page Title
        $this->module_title = 'Commandes';

        // module name
        $this->module_name = 'order';

        // module icon
        $this->module_icon = 'fas fa-cart';

        // module model name, path
        $this->module_model = "Modules\Order\Entities\Order";

        $this->accountingRepo = $accountingRepo;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        $orders = Order::transactions();

        /**
         * Filter if the user wants to see
         * orders from a specific status
         */
        if(request()->has('order_status'))
        {
            $orders = $orders->where('status', request('order_status'));
        }

        return view('order::index',
                        ['module_title' => $this->module_title,
                        'module_name' => $this->module_name,
                        'module_icon' => $this->module_icon,
                        'module_name_singular' => Str::singular($this->module_name),
                        'module_action' => 'List',
                        'orders' => $orders->paginate(20),
                        ]
            );
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('order::backend.create',
        ['module_title' => $this->module_title,
        'module_name' => $this->module_name,
        'module_icon' => $this->module_icon,
        'module_name_singular' => Str::singular($this->module_name),
        'module_action' => 'List',
        'clients' => Client::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $orderAttributes = $request->except(['_token', 'client_id']);
        $order  = [];

        $orderTransactionId = $this->accountingRepo->getTransactionId();
        $orderTotal = 0;

        for($i=0; $i < count($orderAttributes['id']); $i++){

            $totalPrice = $orderAttributes['price'][$i] * $orderAttributes['quantity'][$i];
            $orderTotal = $orderTotal + $totalPrice;

            $order[] = [
                "order_transaction_id" => $orderTransactionId,
                "item_id"              => $orderAttributes['id'][$i],
                "item_name"            => $orderAttributes['name'][$i],
                "quantity"             => $orderAttributes['quantity'][$i],
                "unit_price"           => $orderAttributes['price'][$i],
                "total_price"          => $totalPrice,
                "client_id"            => $request->client_id,
                "status"               => env('PROCESSING_STATUS', "processing"),
                "created_at"           => now(),
                "updated_at"           => now(),
            ];
        }

     // Record transactions
       if(  Order::insert($order)){
        $this->recordPostings($orderTransactionId, $orderTotal, $request->client_id);

        // Notify
        $this->notifyUsers($orderTransactionId, env('PROCESSING_STATUS', "processing"));

        Flash::success("<i class='fas fa-check'></i> New '".Str::singular($this->module_title)."' Added")->important();
        return $this->showInvoice( $orderTransactionId);
       }


       return redirect()->back();
    }

    /**
     * Record postings
     */
    private function recordPostings($orderTransactionId, $orderTotal, $clientId)
    {
        // Now that the order has been saved, let us update postings
        $journalId = env('ORDER_JOURNAL_ID', 1);
        $debitAccountId = env('ORDER_DEBIT_ACCOUNT_ID', 571);
        $creditAccountId = config('ORDER_CREDIT_ACCOUNT_ID', 411);
        $wording = 'Sale - order transaction:'. $orderTransactionId. ' for client:'. $clientId;

        // Debit and credit
        DB::beginTransaction();

        $this->accountingRepo->savePosting($debitAccountId, $orderTotal, $orderTransactionId, 'Debit', $journalId,$wording,$cheque_number = 'N/A');
        $this->accountingRepo->savePosting($creditAccountId, $orderTotal, $orderTransactionId, 'Credit', $journalId,$wording,$cheque_number = 'N/A');

        DB::commit();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showInvoice($id)
    {

        if(request()->has('change_order_status_to'))
        {
            $orderStatus = request('change_order_status_to');

            Order::where('order_transaction_id', $id)->update(['status' => $orderStatus]);

            if($orderStatus === "completed"){
                      // Record purchased stock
            Order::where('order_transaction_id', $id)->get()->each(function($orderLine){
                    Purchase::create([
                            "item_name" => $orderLine['item_name'],
                            "item_qty" => -1 * $orderLine['quantity'],
                            "item_type" => "Cartons",
                            "item_mouvement" => "OUT",
                            "item_status" => "SOLD",
                            "approved_by" => auth()->user()->id,
                            "initiated_at" => now(),
                            "approved_at" => now(),
                            "item_comment" => "Sale #". $orderLine['order_transaction_id'],
                            "userid" => auth()->user()->id
                    ]);

                });
            }

            $this->notifyUsers($id, $orderStatus);
            Flash::success("<i class='fas fa-check'></i> Order Status Updated to '". $orderStatus )->important();
        }

        $orders = Order::where('order_transaction_id', $id)->get();

        return view('order::backend.receipt', compact('orders'));
    }

    /**
     * Notify Users
     *
     * @param integer $orderId
     * @param string $orderStatus
     * @return void
     */
    public function notifyUsers($orderId, $orderStatus) : void
    {
        // Get notification group
        $userGroup = config('order.order_approval_matrix.'. $orderStatus);
        $notificationEmails = config('order.'.  $userGroup);

        // Get the users
        $users = User::whereIn('email', $notificationEmails)->get();

        // Send the notifications
        Notification::send($users, new NewOrderNotification($orderId, $orderStatus));
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

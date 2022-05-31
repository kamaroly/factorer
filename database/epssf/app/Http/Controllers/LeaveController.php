<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Models\Leave;
use Ceb\Models\User;
use Ceb\Models\UserGroup;
use Fenos\Notifynder\Notifynder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    function __construct() {
        parent::__construct();
    }


    /**
     * Display a listing of the resource.
     * GET /leavemanagement
     *
     * @return Response
     */
    public function index()
    {   
       // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.view')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' started to view leaves requests');
    
        $leaves = Leave::paginate(20);
        return view('leaves.pending', array('leaves' => $leaves));
    }
    /**
     * Show the form for creating a new resource.
     * GET /leavemanagement/approve/{leave}
     *
     * @return Response
     */
    public function approve($id,Notifynder $notifier)
    {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.approve')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' started to approve leaves requests');
    
            $leave = Leave::findOrFail($id);
            $leave->status = Leave::$approved;
            $leave->save();
        
            // Send notification
           $notifier->category('leave.approved')
                    ->from($this->user->id)
                    ->to($leave->user_id)
                    ->url(route('leaves.show'))
                    ->send();
             $data['status'] = 'approved';
               Mail::queue('emails.leave', $data, function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject("Loan status changed");
               });
            flash()->success(trans('leave.leave_approved_successfully'));
            return redirect()->route('leaves.pending'); 
    }
    /**
     * Display the specified resource.
     * GET /leavemanagement/reject/{leave}
     *
     * @param  int  $leave
     * @return Response
     */
    public function reject($id,Notifynder $notifier)
    {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.reject')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' started to reject leaves requests');
    
            $leave = Leave::findOrFail($id);
            $leave->status = Leave::$rejected;
            $leave->save();
            
               // Send notification
        $notifier->category('leave.rejected')
                   ->from($this->user->id)
                   ->to($leave->user_id)
                   ->url(route('leaves.show'))
                   ->send();

               $data['status'] = 'approved';
               Mail::queue('emails.leave', $data, function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject("Loan status changed");
               });
            flash()->success(trans('leave.leave_rejected_successfully'));
            
            return redirect()->route('leaves.pending'); 
    }
    /**
     * Display a listing of the logged in user leaves.
     * 
     * GET /leave/show
     *
     * @return Response
     */
    public function show(User $employee)
    {
         // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.view.my.leaves')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing his / her leaves requests');
    
        $userId = $this->user->id;
        $userLeaves = $employee->whereHas('leaves', function($q) use($userId) {
                        $q->where('user_id', '=',$userId);
                    })->get();
        $leaves = count($userLeaves) ? $userLeaves[0]->leaves : null;

        return view('leaves.show', array('leaves' => $leaves));
    }
    /**
     * Show the form for creating a new resource.
     * GET /leave/create
     *
     * @return Response
     */
    public function create()
    {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.request.leaves')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is requesting leave');
    
        $title = trans('leave.new_leave');
        return view('leaves.create',compact('title'));
    }
    /**
     * Store a newly created resource in storage.
     * POST /leave
     *
     * @return Response
     */
    public function store(Request $request,Notifynder $notifier)
    {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.request.leaves')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is requesting leave');
        $data =  $request->all();
        $data['start'] = $request->get('start_year').'-'.$request->get('start_month').'-'.$request->get('start_day') ;    
        $data['end'] =   $request->get('end_year') .'-'.$request->get('end_month').'-'.$request->get('end_day');    

        $validator = Validator::make(
                        $data, [
                            'start' => 'required|date_format:"Y-m-d"',
                            'end' => 'required||date_format:"Y-m-d"',
                            'days'=>'required|numeric',
                            'phone' => 'required',
                            'backup' => 'required|min:6',
                        ]
        );
        if ($validator->fails()) {
            return redirect()->route('leaves.index')->withErrors($validator)->withInput();
        }
      

        $data['user_id'] = $this->user->id;
        $data['status'] = Leave::$applied;
        $data['start'] = date("Y-m-d", strtotime($data['start']));
        $data['end'] = date("Y-m-d", strtotime($data['end']));

        $newLeave = Leave::create($data);
        if ($newLeave) {

        
        // Get all users who have the right to approve leave
        // if we found them then ilitirate them and 
        // make sure, we notify all of them
        $groups = UserGroup::with('users')->get();

        foreach ($groups as $group) {      
            
            // If this group doesn't have access then 
            // go to the next group
            
            if (!$group->hasAccess('leaves.leaves.approve')) {
                continue;
            }

            // Group has access let's notify them
           foreach ($group->users as $user) {
               $notifier->category('leave.leave')
                   ->from($this->user->id)
                   ->to($user->id)
                   ->url(route('leaves.index'))
                   ->sendWithEmail();
           }
        }
        

            return redirect()->route('leaves.index');
        }
        return redirect()->route('leaves.create')->withInput();
    }
    /**
     * Display the specified resource.
     * GET /leave/{leave}
     *
     * @param  int  $leave
     * @return Response
     */
    public function status($id)
    {   
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('leaves.view.leave.status')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' is viewing leave status');
        $leave = Leave::findOrFail($id);

        return view('leaves.status', array('leave' => $leave));
    }
}

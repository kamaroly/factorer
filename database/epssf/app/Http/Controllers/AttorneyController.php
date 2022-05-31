<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;
use Ceb\Repositories\Attorney\AttorneyRepository;
use Ceb\Http\Requests;
use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests\AttorneyRequest;
use Redirect;
use Log;

class AttorneyController  extends Controller
{

    function __construct() {
        parent::__construct();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {

        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('attornies.add')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' started to add attornies');
        $member = $request->get('member');
        $title = trans('attorney.add_attoney');
        return view('attornies.create',compact('member','title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(AttorneyRequest $request,AttorneyRepository $attornies)
    {

        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('attornies.add')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' added attornies');

       $message = $attornies->store($request->all());
       flash()->success($message);
       return Redirect::route('members.show',['members'=>$request->get('member')]);
    }

   
}

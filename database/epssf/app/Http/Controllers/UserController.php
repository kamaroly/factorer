<?php

namespace Ceb\Http\Controllers;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Models\User;
use Ceb\Repositories\Member\MemberRepositoryInterface;
use Illuminate\Http\Request;
use Sentinel\Repositories\Group\SentinelGroupRepositoryInterface;
use Vinkla\Hashids\HashidsManager;

class UserController extends Controller
{
	public $users;

    function __construct(
        User $user,
        SentinelGroupRepositoryInterface $groupRepository,
        HashidsManager $hashids
    ) {
        $this->user  = $user;
        $this->groupRepository = $groupRepository;
        $this->hashids         = $hashids;
        parent::__construct();
    }

    /**
     * Show all users in the database
     * @param  User   $users 
     * @return  view
     */
    public function index(Request $request)
    {
    	if ($request->has('query')) {	
	    	$users = \Ceb\Models\User::search($request->input('query'))->paginate(10);
    	}
    	else
    	{
    	  $users = $this->user->paginate(10);
        }
        return view('sentinel.users.index', compact('users'));
    }

    /**
     * Show one selected user from the databse
     * @param  string $userId 
     * @return mixed
     */
    public function edit($hash)
    {
    	 // Decode the hashid
        $id = $this->hashids->decode($hash)[0];

        // Get the user
        $user = $this->user->findOrFail($id);

        // Get all available groups
        $groups = $this->groupRepository->all();

    	return view('sentinel.users.edit',compact('user','groups'));
    }
}

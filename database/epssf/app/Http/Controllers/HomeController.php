<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;
use Ceb\Http\Requests;
use Ceb\Http\Controllers\Controller;

class HomeController extends Controller
{
    function __construct() {
    	
    	parent::__construct();
    }

    public function index()
    {
    	// If this person is a ceb MEMBER then take him to his profile
    	if ($this->user->isNormalMember()) {
    		return redirect()->route('members.show',$this->user->id);
    	}

    	return view('partials.dashboard');
    }
}

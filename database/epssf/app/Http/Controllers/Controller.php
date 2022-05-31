<?php

namespace Ceb\Http\Controllers;
use App;
use Ceb\Models\Setting;
use Flash;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Sentry;

abstract class 	Controller extends BaseController {
	use DispatchesJobs, ValidatesRequests;

	public $user;
	public $flash;
	function __construct() {

		$this->middleware('sentry.auth');
		$this->flash = new Flash;
		$this->user = Sentry::getUser();
		$this->setting = new Setting;

		if (!is_null($this->user)) 
		{
		   App::setLocale($this->user->language);
		}

        // Remove any flash available
        flash()->clear();
	}

	/**
	 * Parse csv files
	 * @param   $uploadedFileName 
	 * @return  
	 */
	public function parseCsv($uploadedFileName)
	{
	    $path = request()->file($uploadedFileName)->getRealPath();
	    // Return data from csv
	    $csvRows = collect(array_map('str_getcsv', file($path)));
	    $columns = $csvRows->first();

	    // Transform csv and map each row with the columns, skip the first row
	    // because it has column names
	    return $csvRows->except(0)->transform(function($csvRow,$key) use($columns){
			       	    
			       	    foreach ($csvRow as $key => $cell) {
			    	    	$csvRow[strtolower($columns[$key])] = $cell;
			    	    	// Remove the 
			    	    	unset($csvRow[$key]);
			    	    }
			    	return $csvRow;
			    });
	}
}
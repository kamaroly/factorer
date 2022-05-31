<?php

namespace Ceb\Http\Controllers;

use Illuminate\Http\Request;
use Ceb\Http\Requests;
use Ceb\Http\Controllers\Controller;

class UtilityController extends Controller
{
   function __construct() {
       parent::__construct();
   }

   public function backup()
   {
       
       // First check if the user has the permission to do this
        if (!$this->user->hasAccess('utility.can.do.database.backup')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        $dbhost = env('DB_HOST');
        $dbuser = env('DB_USERNAME');
        $dbpass = env('DB_PASSWORD');
        $dbname = env('DB_DATABASE');
        $fileName = $dbname.'_backup_'.date('Y-M-d').'.gz';
        $pathToFile = storage_path('app').'/'.$fileName;

        $mysqldump=exec('which mysqldump');
        $path = exec('pwd');

        $command = "$mysqldump --opt -h $dbhost --user='$dbuser' --password='$dbpass'  $dbname |gzip > $pathToFile";
        exec($command);
        flash()->success(trans('general.database_backup_has_been_done_at').date('Y-M-d h:i:s'));
        return response()->download($pathToFile);
   }
}

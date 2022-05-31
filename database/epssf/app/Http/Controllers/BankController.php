<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Http\Requests\BankRequest;
use Ceb\Models\Institution;
use Ceb\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    function __construct(Bank $bank) {
        $this->bank = $bank;
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.view')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . 'started to view banks');
    
        $banks = $this->bank->paginate(20);
        return view('settings.bank.list',compact('banks'));
    }

   
   /**
    * Show existing institution for update
    * @param  $id unique identifier for the institution
    * @return  
    */
   public function edit($id)
   {
     // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.edit')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
       $bank = $this->bank->findOrFail($id);
       $title       = trans('bank.update');
       return view('settings.bank.update',compact('bank','title'));
   }


   /**
    * Try to update an institution
    * @param  InstitutionRequest $request 
    * @param               $id      
    * @return   view
    */
   public function update(BanknRequest $request,$id)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.edit')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
      
       $bank = $this->bank->findOrFail($id);
       $bank->name = $request->get('bank_name');

       if ($bank->save()) {
          flash()->success(trans('bank.bank_is_updated_successfully'));
          Log::info($this->user->email . ' updated bank :'.$bank->bank_name);

          return redirect()->route('settings.institution.index');
      } 

      flash()->error(trans('bank.error_occured_while_trying_to_update_bank'));

      return redirect()->route('settings.bank.index'); 
   }

   /**
    * Show form to create new institution
    * @param  $id unique identifier for the institution
    * @return  
    */
   public function create()
   {
     // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.create')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

       $bank = new Bank;
       $title    = trans('bank.create');
       return view('settings.bank.create',compact('bank','title'));
   }


   public function store(BankRequest $request)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.create')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        $bank = new Bank;

        $bank->bank_name = $request->get('bank_name');


        if ($bank->save()) {
          flash()->success(trans('bank.bank_has_been_added_successfully'));
          Log::info($this->user->email . ' create bank :'.$bank->name);

          return redirect()->route('settings.bank.index');
        }


      flash()->error(trans('bank.error_occured_while_trying_to_add_new_bank'));

      return redirect()->route('settings.bank.index'); 
   }


   public function destroy($id)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.delete')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

         $bank = $this->bank->findOrFail($id);
         
         if ($bank->memberCount() > 0 ) {
             flash()->warning(trans('bank.the_bank_you_are_trying_to_remove_has_members_please_remove_members_first'));
             Log::info($this->user->email . ' bank you are trying to remove has'.$bank->memberCount().' members');   
            return redirect()->route('settings.bank.index'); 
         }

         $name = $bank->name;

         if ($bank->delete()) {
           
             flash()->success(trans('bank.you_have_successfully_removed_bank'));
             Log::info($this->user->email . ' has deleted institution:'.$name);   
             return redirect()->route('settings.bank.index'); 
         }

          flash()->error(trans('bank.error_occred_while_trying_to_remove_bank'));  
          return redirect()->route('settings.bank.index'); 
   }
}

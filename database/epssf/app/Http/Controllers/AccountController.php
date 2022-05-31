<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Http\Requests\AccountRequest;
use Ceb\Models\Account;
use Illuminate\Http\Request;
use Log;

class AccountController extends Controller
{   
    function __construct(Account $account) {
        $this->account = $account;
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
        if (!$this->user->hasAccess('account.list')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' viewed accounts list');
        $accounts = $this->account->orderBy('account_number','ASC')->paginate(20);
        return view('settings.accountingplan.list',compact('accounts'));
    }


   /**
    * Show existing account for update
    * @param  $id unique identifier for the account
    * @return  
    */
   public function edit($id)
   {
     // First check if the user has the permission to do this
        if (!$this->user->hasAccess('account.edit')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
       $account = $this->account->findOrFail($id);
       $title       = trans('account.update');
       return view('settings.accountingplan.update',compact('account','title'));
   }


   /**
    * Try to update an account
    * @param  accountRequest $request 
    * @param               $id      
    * @return   view
    */
   public function update(AccountRequest $request,$id)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('account.edit')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
      
       $account = $this->account->findOrFail($id);
       $account->account_number = $request->get('account_number');
       $account->entitled       = $request->get('entitled');
       $account->account_nature = $request->get('account_nature');

       if ($account->save()) {
          flash()->success(trans('account.account_is_updated_successfully'));
          Log::info($this->user->email . ' updated account :'.$account->entitled);
          return redirect()->route('settings.accountingplan.index');
      } 

      flash()->error(trans('account.error_occured_while_trying_to_update_account'));

      return redirect()->route('settings.accountingplan.index'); 
   }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('account.create')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        $title = trans('account.add_new_account');
        $account = new Account;
     
        return view('settings.accountingplan.create',compact('account','title'));
    }


    /**
     * Store account in the database
     * @param  AccountRequest $request 
     * @param           $id      
     * @return       view
     */
    public function store(AccountRequest $request)
    {
         // First check if the user has the permission to do this
        if (!$this->user->hasAccess('account.create')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        $account = new Account;
        $account->account_number = $request->get('account_number');        
        $account->entitled       = $request->get('entitled');       
        $account->account_nature = $request->get('account_nature');

        if ($account->save()) {
          flash()->success(trans('account.new_account_has_been_added_successfully'));
          Log::info($this->user->email . ' create new account :'.$account->account_number.'-'.$account->entitled);
          return redirect()->route('settings.accountingplan.index');
        }

        flash()->success(trans('account.something_went_wrong_while_adding_new_account'));
        return view('settings.accountingplan.index',compact('account','title'));
    }


    public function destroy($id)
    {
              // First check if the user has the permission to do this
        if (!$this->user->hasAccess('account.delete')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
      
       $account = $this->account->findOrFail($id);


         if ($account->postings->count() > 0 ) {
             flash()->warning(trans('account.the_account_you_are_trying_to_delete_has_postings_please_contact_your_admin'));
             Log::info($this->user->email . ' account you are trying to remove has '.$account->postings->count().' postings');   
            return redirect()->route('settings.accountingplan.index'); 
         }

         $name = $account->name;

         if ($account->delete()) {
           
             flash()->success(trans('account.you_have_successfully_removed_account'));
             Log::info($this->user->email . ' has deleted account:'.$name);   
             return redirect()->route('settings.accountingplan.index'); 
         }

          flash()->error(trans('account.error_occred_while_trying_to_remove_account'));  
          return redirect()->route('settings.accountingplan.index'); 
  
    }
}

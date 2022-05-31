<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Http\Requests\InstitutionRequest;
use Ceb\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstitutionController extends Controller
{
    function __construct(Institution $institution) {
        $this->institution = $institution;
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
        Log::info($this->user->email . ' started to view institutions');
    
        $institutions = $this->institution->paginate(20);
        return view('settings.institution.list',compact('institutions'));
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
       $institution = $this->institution->findOrFail($id);
       $title       = trans('institution.update');
       return view('institutions.update',compact('institution','title'));
   }


   /**
    * Try to update an institution
    * @param  InstitutionRequest $request 
    * @param               $id      
    * @return   view
    */
   public function update(InstitutionRequest $request,$id)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.edit')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
      
       $institution = $this->institution->findOrFail($id);
       $institution->name = $request->get('name');

       if ($institution->save()) {
          flash()->success(trans('institution.institution_is_updated_successfully'));
          Log::info($this->user->email . ' updated institution :'.$institution->name);

          return redirect()->route('settings.institution.index');
      } 

      flash()->error(trans('institution.error_occured_while_trying_to_update_institution'));

      return redirect()->route('settings.institution.index'); 
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

       $institution = new Institution;
       $title       = trans('institution.create');
       return view('institutions.create',compact('institution','title'));
   }


   public function store(InstitutionRequest $request)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.create')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        $institution = new Institution;

        $institution->name = $request->get('name');


        if ($institution->save()) {
          flash()->success(trans('institution.institution_has_been_added_successfully'));
          Log::info($this->user->email . ' create institution :'.$institution->name);

          return redirect()->route('settings.institution.index');
        }


      flash()->error(trans('institution.error_occured_while_trying_to_add_new_institution'));

      return redirect()->route('settings.institution.index'); 
   }


   public function destroy($id)
   {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('institutions.delete')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

         $institution = $this->institution->findOrFail($id);
         
         if ($institution->memberCount() > 0 ) {
             flash()->warning(trans('institution.the_instition_you_are_trying_to_remove_has_members_please_remove_members_first'));
             Log::info($this->user->email . ' institution you are trying to remove has'.$institution->memberCount().' members');   
            return redirect()->route('settings.institution.index'); 
         }

         $name = $institution->name;

         if ($institution->delete()) {
           
             flash()->success(trans('institution.you_have_successfully_removed_institution'));
             Log::info($this->user->email . ' has deleted institution:'.$name);   
             return redirect()->route('settings.institution.index'); 
         }

          flash()->error(trans('institution.error_occred_while_trying_to_remove_institution'));  
          return redirect()->route('settings.institution.index'); 
   }
}

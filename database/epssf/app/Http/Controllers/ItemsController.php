<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Http\Requests;
use Ceb\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ItemsController extends Controller
{
   function __construct(Item $item){
        parent::__construct();
        $this->item = $item;
   }

    /**
     * Get list of the resources
     * 
     * @return  view
     */
    public function index() {
        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('items.index')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }
      $items = $this->item->paginate(20);
      return view('items.list',compact('items'));
    }

    /**
     * Add new resource to the database
     * 
     * @param Request $request
     */
    public function add(Request $request) 
    {
        $item = new Item;
        if (!empty($data  = $request->all())) 
        {
            // First check if the user has the permission to do this
            if (!$this->user->hasAccess('items.add')) {
                flash()->error(trans('Sentinel::users.noaccess'));

                return redirect()->back();
            }

            $insert = $this->item->insert($data);
            if ($insert === TRUE) 
            {
                flash()->success(trans('item.item_added'));
                return redirect()->route('items.index');
            } 

            return redirect()->back()->withErrors($insert)->withInput();
        }
        return view('items.add',compact('item'));
    }

    /**
     * Edit an Item in the database
     * @param  integer $id
     * @return 
     */
    public function edit($id) {
        // Find by ID first
        $item = $this->item->find($id);
        if (empty($item)) 
        {
            flash()->error(trans('item.we_could_not_find_the_item_you_are_looking_for'));
            return redirect()->route('items.index');
        } 

        if (!empty($item) && !empty($data = Input::all())) 
        {   
            // First check if the user has the permission to do this
            if (!$this->user->hasAccess('items.edit')) {
                flash()->error(trans('Sentinel::users.noaccess'));
                return redirect()->back();
            }

            $update = $item->modify($data);
            if ($update === TRUE) 
            {
                flash()->success(trans('item.item_item_updated'));
                return redirect()->route('items.index');
            } 

            return redirect()->route('items.edit',['id'=>$id])
                                ->withErrors($update)
                                ->withInput();
        }

        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('items.view')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }

        return view('items.add', compact('item'));
    }

    /**
     * Remove item from the database
     * @param  integer $id 
     * @return  redirect
     */
    public function delete($id) {

        // First check if the user has the permission to do this
        if (!$this->user->hasAccess('items.delete')) {
            flash()->error(trans('Sentinel::users.noaccess'));
            return redirect()->back();
        }
        
        // Find by ID first
        $item = $this->item->find($id);
        if (!empty($item)) {
            $remove = $item->delete();
            if ($remove !== TRUE) 
            {
               return redirect()->back();
            }
        }

        flash()->warning('item.item_deleted');
        return redirect()->route('items.index');        
    }
}

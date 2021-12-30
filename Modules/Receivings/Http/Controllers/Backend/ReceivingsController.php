<?php

namespace Modules\Receivings\Http\Controllers\Backend;

use Log;
use Auth;
use Flash;
use Carbon\Carbon;
use App\Authorizable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Contracts\Support\Renderable;
use Modules\Comment\Notifications\NewCommentAdded;
use Modules\Comment\Http\Requests\Backend\CommentsRequest;

class ReceivingsController extends Controller
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Comments';

        // module name
        $this->module_name = 'comments';

        // module icon
        $this->module_icon = 'fas fa-comments';

        // module model name, path
        $this->module_model = "Modules\Receivings\Entities\Receiving";
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        dd('tet');
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';

        $$module_name = $module_model::paginate();

        Log::info(label_case($module_title.' '.$module_action).' | User:'.Auth::user()->name.'(ID:'.Auth::user()->id.')');

        return view(
            "receivings::backend.$module_name.index_datatable",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_name_singular', 'module_action')
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('receivings::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('receivings::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('receivings::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}

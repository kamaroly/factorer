<?php

namespace Ceb\Http\Controllers;

use Ceb\Http\Controllers\Controller;
use Ceb\Models\File as FileModel;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Request;

class FileController extends Controller {

	public $file;
	public $storage;
	function __construct(FileModel $file, Storage $storage) {
		$this->middleware('sentry.auth');
		$this->file = $file;
		$this->storage = $storage;
		parent::__construct();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		  // First check if the user has the permission to do this
        if (!$this->user->hasAccess('files.view')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log
        Log::info($this->user->email . ' started to view files');
		$entries = $this->file->paginate(20);

		return view('files.index', compact('entries'));
	}

	public function add() {

	  // First check if the user has the permission to do this
        if (!$this->user->hasAccess('files.add')) {
            flash()->error(trans('Sentinel::users.noaccess'));

            return redirect()->back();
        }

        // First log 
        Log::info($this->user->email . ' started to add a file');
		$file = Request::file('filefield');

		$extension = $file->getClientOriginalExtension();

		$filename = time() . $file->getFilename();
		$this->storage->disk('local')->put($filename . '.' . $extension, File::get($file));

		$entry = $this->file;

		$entry->mime = $file->getClientMimeType();

		$entry->original_filename = $filename;

		$entry->filename = $filename . '.' . $extension;

		$entry->save();

		return redirect('files');

	}

	/**
	 * Get file from the diesk
	 * @param  string $filename 
	 * @return file           
	 */
	public function get($filename=null) {

      try
      {
		$entry = $this->file->where('filename', '=', $filename)->first();
		// If we don't have a file, return this dumy path
		if (is_null($entry)) {
			return redirect(url('assets/dist/img/no-image.png'));
		}

		$file = $this->storage->disk('local')->get($entry->filename);

		return (new Response($file, 200))
			->header('Content-Type', $entry->mime);
		}
		catch(FileNotFoundException $e)
		{
			return redirect(url('assets/dist/img/no-image.png'));
		}
	}

}

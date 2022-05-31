<?php namespace Ceb\Repositories\Attorney;

use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Ceb\Models\Attorney;
use Ceb\Models\User;
use Ceb\Traits\FileTrait;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Events\Dispatcher;
use Sentinel\DataTransferObjects\BaseResponse;
use Sentinel\DataTransferObjects\FailureResponse;
use Str;

class AttorneyRepository {

	use FileTrait;

	protected $dispatcher;
	protected $attorney;
	protected $config;
	protected $sentry;
	protected $storage;
	protected $users;
	/**
	 * Construct a new SentryUser Object
	 */
	public function __construct(Sentry $sentry,User $users, Attorney $attorney,Storage $storage, Repository $config, Dispatcher $dispatcher, User $user) {
		$this->dispatcher = $dispatcher;
		$this->attorney = $attorney;
		$this->config = $config;
		$this->users = $users;
		$this->sentry = $sentry;
		$this->storage = $storage;

	}

	/**
	 * Store a newly created user in storage.
	 *
	 * @return BaseResponse
	 */
	public function store($formData) {
		try {
			//Prepare the member data
			//Make sure data passed is array
			$data = (array) $formData;
			// Let's make sure we are not inserting an empty column in the database;
			foreach ($data as $key => $value) {
				if (is_null($value) || empty($value) || trim($value) == '') {
					unset($data[$key]);
				}
			}

			// Trying to upload the attached images
			$filename = time() . $this->slug($data['names']);

			$data['photo'] = $this->addImage($formData['photo'], $filename . '-photo');
			$data['signature'] = $this->addImage($formData['signature'], $filename . '-signature');

			// Attempt user registration
			$member = $this->users->findOrFail($data['member']);

			$member->attornies()->create($data);

			// User registration was successful.  Determine response message
			$message = trans('attorney.created');

			// Fire the 'user registered' event
			$this->dispatcher->fire('attorney.registered');

			// Return a response
			return $message;
		} catch (UserExistsException $e) {
			return $message = trans('attorney.exists');
		}
	}
}

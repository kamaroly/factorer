<?php namespace Ceb\Repositories\Member;

use Cartalyst\Sentry\Facades\Laravel\Sentry as AuthenticatedUser;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;

use Ceb\Models\MonthlyFeeInventory;
use Ceb\Models\User;
use Ceb\Models\Loan;

use Ceb\Traits\FileTrait;
use Ceb\Traits\TransactionTrait;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Sentinel\DataTransferObjects\BaseResponse;
use Sentinel\DataTransferObjects\FailureResponse;
use Str;

class MemberRepository implements MemberRepositoryInterface {

	use FileTrait;
	use TransactionTrait;

	protected $dispatcher;
	protected $user;
	protected $config;
	protected $sentry;
	protected $storage;

	/**
	 * Construct a new SentryUser Object
	 */
	public function __construct(Sentry $sentry, Storage $storage, Repository $config, Dispatcher $dispatcher, User $user) {
		$this->dispatcher = $dispatcher;
		$this->user = $user;
		$this->config = $config;
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

			// Get the unique adhersion number for this member
			$data['adhersion_id'] = $this->generateAdhersionNumber();
			$data['contract_id']  = $data['adhersion_id'];

			// Setting default password
			$data['password'] = e('$2y$10$hXKCE6NfWWHBxt1VTeBCxOnHkRDx2jfkbR0JkGwu1OWFwQ.wPDmJG');
							// Extract First name and last name
			$data['first_name'] = trim($data['first_name']);
         	$data['last_name'] = trim($data['last_name']);

						// Let's make sure we are not inserting an empty column in the database;
			foreach ($data as $key => $value) {
				if (is_null($value) || empty($value) || trim($value) == '') {
					unset($data[$key]);
				}
			}

			// Trying to upload the attached images
			$filename = time() . $this->slug($data['first_name']) . $data['adhersion_id'];

			try
			{
				$data['photo'] = $this->addImage($formData['photo'], $filename . '-photo');
				$data['signature'] = $this->addImage($formData['signature'], $filename . '-signature');
			}
			catch(\Exception $e)
			{
				// User image or photo cannot be loaded put a default one.
				$data['photo'] = 'no-image.png';
				$data['photo'] = 'no-image.png';
			}

			// Remove unnecessary information
			$dataToInsert = [];
			foreach ($this->user->fillable as $key => $value) {
			 if (array_key_exists($value, $data)) {
			      $dataToInsert[$value]  = $data[$value];
				}
			}
			
			$dataToInsert['created_at'] = date('Y-m-d h:i:s');
			// Attempt user registration
			$insertId = Db::table('users')->insertGetId($dataToInsert);
			$user = $this->sentry->getUserProvider()->findById($insertId);

			// record the history of the inventory
			// 
			$inventory =  new MonthlyFeeInventory;
			$inventory->amount = $user->monthly_fee;
			$inventory->adhersion_id = $user->adhersion_id;

			$inventory->save();

			// If no group memberships were specified, use the default groups from config
			if (array_key_exists('groups', $data)) {
				$groups = $data['groups'];
			} else {
				$groups = $this->config->get('sentinel.default_user_groups', []);
			}

			// Assign groups to this user
			foreach ($groups as $name) {
				$group = $this->sentry->getGroupProvider()->findByName($name);
				$user->addGroup($group);
			}

			// User registration was successful.  Determine response message
			$message = trans('member.created');

			// Response Payload
			$payload = [
				'user' => $user,
				'activated' => 0,
			];

			// Fire the 'user registered' event
			$this->dispatcher->fire('member.registered', $payload);

			// Return a response
			return $message;
		} catch (UserExistsException $e) {
			return $message = trans('member.exists');
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  array $data
	 *
	 * @return BaseResponse
	 */
	public function update($data) {
		try {
			// Find the user using the user id
			$user = $this->sentry->findUserById($data['id']);

			// Update User Details
			$user->email = (isset($data['email']) ? e($data['email']) : $user->email);
			$user->username = (isset($data['username']) ? e($data['username']) : $user->username);

			// Extract First name and last name
			//$data['first_name'] = trim(substr($data['first_name']));
			//$data['last_name'] = trim(substr($data['last_name']));

			if (empty($user->contract_id)) {
				$data['contract_id'] = 'CONTRACT  ' . date('YmdHis') . (string) AuthenticatedUser::getUser()->id;
			}

			// Trim any space
			$data['first_name'] = trim($data['first_name']);
			$data['last_name'] = trim($data['last_name']);

			//Clean data by  Remove tocket and Method and names fields
			unset($data['_method']);
			unset($data['_token']);

			// Trying to upload the attached images
			$filename = time() . $this->slug($data['first_name']) . $user->adhersion_id;

			$data['photo'] = isset($data['photo']) ? $this->addImage($data['photo'], $filename . '-photo') : null;
			$data['signature'] = isset($data['signature']) ? $this->addImage($data['signature'], $filename . '-signature') : null;

			//unset($data['first_name']);

			$existingMemberMonthlyFees = 0;
			
			// Make sure we are tracking all changes of the monthly
			// Fee changes
			if ((int) $user->monthly_fee <> (int) $data['monthly_fee']) {

				$inventory               =  new MonthlyFeeInventory;
				$inventory->amount       = $data['monthly_fee'];
				$inventory->adhersion_id = $user->adhersion_id;

				// Determine if this is  increasing or
				// decreasing modification
				$inventory->type         = 'decrease';
				if ($user->monthly_fee < $data['monthly_fee']) {
					$inventory->type     = 'increase';
				}

				$inventory->save();

				// Update contract ID since amount has changed!
				$data['contract_id'] = 'CONTRACT  ' . date('YmdHis') . (string) AuthenticatedUser::getUser()->id;
			}

			// Start setting new data and update them here.
			foreach ($data as $key => $value) {

				// if this field is empty go to the next one
				if (is_null($value) || empty($value) || trim($value) == '') {
					continue;
				}

				$user->$key = (isset($data[$key]) ? e($data[$key]) : $user->$key);
			}

			// Update the user
			if ($user->save()) {
				// User information was updated
				$this->dispatcher->fire('member.updated', ['user' => $user]);

				return trans('member.updated');
			}

			return new FailureResponse(trans('Sentinel::users.notupdated'), ['user' => $user]);
		} catch (UserExistsException $e) {
			return $message = trans('Sentinel::users.exists');
		} catch (UserNotFoundException $e) {
			return $message = trans('Sentinel::sessions.invalid');
		}
	}

	/**
	 * Get Members with Emergencies
	 * @return query builder
	 */
	public function withEmergency()
	{
		return User::with('loans', function($query){
								$query->where('operation_type','=','emergency_loan')
				                     ->where('is_umergency',1)
				                     ->where('status','approved');
						}, '=',1);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return BaseResponse
	 */
	public function destroy($id) {
		try {
			// Find the user using the user id
			$user = $this->sentry->findUserById($id);

			// Delete the user
			if ($user->delete()) {
				//Fire the sentinel.user.destroyed event
				$this->dispatcher->fire('sentinel.user.destroyed', ['user' => $user]);

				return trans('Sentinel::users.destroyed');
			}

			// Unable to delete the user
			return trans('Sentinel::users.notdestroyed');
		} catch (UserNotFoundException $e) {
			return $message = trans('Sentinel::sessions.invalid');
		}
	}

	/**
	 * Return the all active user
	 *
	 * @return user object
	 */
	public function all() {
		return $this->all();
	}
	/**
	 * Return the current active user
	 *
	 * @return user object
	 */
	public function getUser() {
		return $this->sentry->getUser();
	}

	/**
	 * Get list of members with at least 1 emergency
	 * @return [type] [description]
	 */
	public function withEmergencies()
	{
		// 1. Get loan with emergencies
		$emergencies = Loan::where('operation_type','=','emergency_loan')
		                     ->where('is_umergency',1)
		                     ->where('status','approved')
		                     ->distinct()
		                     ->select('adhersion_id')->get();
         // Change to array so that it can be done on where condition
		$emergencyAdhersions = $emergencies->pluck('adhersion_id')->toArray();

		// Get Members associated with these loands
		return $this->user->whereIn('adhersion_id',$emergencyAdhersions);
	}

	/**
	 * Search against member
	 * @param  string $keyword
	 * @return mixed
	 */
	public function search($keyword) {
     
     $keyword = strtolower(trim($keyword));
	 $members = $this->user->where(DB::raw("CONCAT(LOWER(trim(first_name)),' ', LOWER(trim(last_name)))"), 'LIKE', '%' . $keyword . '%')
		            ->orWhere('member_nid', 'LIKE', '%' . $keyword . '%')
		            ->orWhere('adhersion_id', 'LIKE', '%' . $keyword . '%')
		            ->orWhere('employee_id', 'LIKE', '%' . $keyword . '%')
		            ->whereNotNull('service')
		            ->take(10)
		            ->get();
	return $members->transform(function($member){

			return [
					'id'	=> $member->id,
					'photo' => $member->photo,
					'adhersion_id' => $member->adhersion_id,
					'first_name' => $member->first_name,
					'last_name' => $member->last_name,
					'member_nid' => $member->member_nid,
					'service' => $member->service,
					'institution' =>$member->institution_name,
					'employee_id' =>$member->employee_id,
				 ];
	});
		       
	}

	/**
	 * Get member by adhersion
	 * @param  integer $adhersionId adhersionId
	 * @return Eloquent Model
	 */
	public function getByAdhersion($adhersionId) {
		return $This->user->getByAdhersion($adhersionId);
	}
	
	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed $identifier
	 *
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveById($identifier) {
		return $this->findOrfail($identifier);
	}

	/**
	 * Generate unique Adhersion number
	 */
	protected function generateAdhersionNumber() {
		// Get latest adhersion id
		return $this->user->generateAdhersionID();
	}

}

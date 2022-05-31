<?php namespace Ceb\Repositories\Member;

interface MemberRepositoryInterface {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param $data
	 *
	 * @return BaseResponse
	 */
	public function store($data);

	/**
	 * Update the specified resource in storage.
	 *
	 * @param $data
	 *
	 * @return BaseResponse
	 */
	public function update($data);

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return BaseResponse
	 */
	public function destroy($id);

	/**
	 * Return all the registered users
	 *
	 * @return Collection
	 */
	public function all();

	/**
	 * Return the currently active user
	 *
	 * @return User
	 */
	public function getUser();

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed $identifier
	 *
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveById($identifier);

}?>
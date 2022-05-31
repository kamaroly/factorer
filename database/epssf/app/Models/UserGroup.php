<?php 

namespace Ceb\Models;
use Cartalyst\Sentry\Groups\Eloquent\Group as GroupModel;
/**
* 
*/
class UserGroup extends GroupModel
{
	protected $table = 'groups';


	/**
	 * Returns the relationship between groups and users.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany(static::$userModel, static::$userGroupsPivot,'group_id','user_id');
	}

	/**
	 * See if a group has access to the passed permission(s).
	 *
	 * If multiple permissions are passed, the group must
	 * have access to all permissions passed through, unless the
	 * "all" flag is set to false.
	 *
	 * @param  string|array  $permissions
	 * @param  bool  $all
	 * @return bool
	 */
	public function scopeHasRight($query,$permissions, $all = false)
	{
		return $this->hasAccess($permissions,$all);
	}
}
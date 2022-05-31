<?php

namespace Ceb\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Ceb\Traits\LogsActivity;
use Spatie\Activitylog\LogsActivityInterface;
use Ceb\Traits\EloquentDatesTrait;

class Model extends Eloquent /* implements LogsActivityInterface */{
	// use LogsActivity;

	use EloquentDatesTrait;
}
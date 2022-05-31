<?php

namespace Ceb\Console\Commands;

use DB;
use Carbon\Carbon;
use Ceb\Models\User;
use Illuminate\Console\Command;

class MarkInactiveMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ceb:mark-member:inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically change member status to inactif for 3 months without contributions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Looking for people who did not do contributions for last 3 months');

        // GET PEOPLE WHO HASN'T CONTRIBUTED IN LAST 3 MONTHS
        $contributors = "SELECT users.adhersion_id  FROM `contributions`, users 
                         WHERE contributions.adhersion_id = users.adhersion_id 
                         AND users.status = ? GROUP BY users.adhersion_id
                         HAVING count(contributions.id) <= 0 OR MAX(CAST(concat(year,'-',month,'-01') AS DATE)) < ?";
        
        $chunMembers  = collect(DB::select($contributors, ['actif',Carbon::now()->subMonth(3)->format('Y-m-01')]));
        $this->warn($chunMembers->count(). ' members did not contribute in last 3 months');

        // Marking users as inactif
        User::whereIn('adhersion_id',$chunMembers->pluck('adhersion_id'))->update(['status' => 'inactif']);
        $this->info($chunMembers->count(). ' members marked as inactive, please contact administrator to activate them');
    }
}

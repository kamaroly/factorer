<?php

namespace Ceb\Jobs;

use Ceb\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class FreshCommand extends Job implements SelfHandling
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}

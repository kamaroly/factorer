<?php

namespace Ceb\Console\Commands;

use Ceb\Models\MemberLoanCautionneur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GuarantorReleaser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ceb:release:guarantor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Releases guarantors whom their members have enough money to pay for loan balance.';

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
        // 1. Get all members with active guarantors 
        $cautions = (new MemberLoanCautionneur)->activeMembers();

        $this->info("We are going to process ".$cautions->count()." cautions");

        // Display progress bar
        $bar = $this->output->createProgressBar($cautions->count());
       // 2. Go through each member and see if mamber can cover his/her own loan,
        $cautions->each(function($caution) use($bar) {
             $member = $caution->member;

            // 3. If the user can cover her/his own loan, then set all guarantors 
            //    cautionneur balance to 0 otherwise move to the next
            //    Only do this for approved loands
            if($member->can_release_guarantor){
                // Release Guarantors for this member
                $releasedGuarantors = $member->releaseGuarantors();
                Log::info(' All '.$releasedGuarantors.' guarantors of '.$member->names.'('.$member->adhersion_id.') are released.');
            }
            // Update progress bar
            $bar->advance();
        });

        $bar->finish();
        $this->info(Date('Y-m-d H:i:s').' Releasing Guarantors for Today is Done.');

        // 5. We are treating emergency cases
        // 1. Get all members with active guarantors 
        $cautions = (new MemberLoanCautionneur)->activeLoans();

        // Display progress bar
        $bar = $this->output->createProgressBar($cautions->count());
       // 2. Go through each member and see if mamber can cover his/her own loan,
        $cautions->each(function($caution) use($bar) {
            // 4. EMERGENCY LOAN ARE TREATED IN SPECIAL WAY,
            //    Close it if balance is 0
            try {
                if( $caution->loan->is_emergency_loan && $caution->loan->balance() <= 0 ){
                    $caution->refunded_amount += ( $caution->amount - $caution->refunded_amount );
                    $caution->save();
                }                
            } catch (\Exception $e) {
                // Release cautionneurs if the loan is not available
                if($caution->loan == NULL ){
                    $caution->refunded_amount += ( $caution->amount - $caution->refunded_amount );
                    $caution->save();
                }
            }

            // Update progress bar
            $bar->advance();
        });

        $bar->finish();
        $this->info(Date('Y-m-d H:i:s').' Releasing Guarantors for Today is Done.');
    }
}

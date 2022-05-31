<?php

namespace Ceb\Http\Controllers;


use Ceb\Models\User;
use Ceb\Models\Loan;
use Ceb\Http\Requests;
use Illuminate\Http\Request;
use Ceb\Http\Controllers\Controller;
use Ceb\Repositories\Member\MemberReportRepository;
use Ceb\Models\MemberLoanCautionneur;

class MemberReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function HigherLoanThanContribution(Loan $loan ,$institution,$export=0)
    {
        $members = $loan->getHigherLoanThanContribution();
       
        $report  = view('reports.member.higher_loan_than_contribution',compact('members'))->render();

        if ($export==1) {
             toExcel($report,'members_all_details_'.request()->segment(4));
        }

        if (request()->has('pdf')) {
            return  htmlToPdf($report);
        }

        return view('layouts.printing', compact('report'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allDetails($institution,$export=0)
    {
        $members = User::with(['contributions','loans'])->byInstitution($institution)->get();

        $report  = view('reports.member.membersalldetails',compact('members'))
                   ->render();
        $this->shouldExport($report,$export);

        return view('layouts.printing', compact('report'));
    }

    /**
     * Get contribution count per member 
     * @param   $institution 
     * @return  
     */
    public function countributionCount($institution,$export=0)
    {        
        $members  = User::with('contributions')->byInstitution($institution)->get();
       $report   =  view('reports.member.contributions_count',compact('members'))->render();
        $this->shouldExport($report,$export);

        return view('layouts.printing', compact('report'));
    }

    /**
     * Export if user wants to Export
     * @param  string|html  $report report to export
     * @param  integer $export confirm if we should export to excel
     * @return void
     */
    public function shouldExport($report,$export)
    {
        if ($export==1) {
             toExcel($report,'members_all_details_'.request()->segment(4));
        }

        if (request()->has('pdf')) {
            return  htmlToPdf($report);
        }
    }
}

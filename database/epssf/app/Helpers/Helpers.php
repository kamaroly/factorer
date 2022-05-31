<?php 
use Ceb\Models\Loan;
use Ceb\Models\MemberLoanCautionneur;

if (! function_exists('moneyFormat')) {
	
	/**
	 * Display number as money
	 * @param  int    $money 
	 * @return string
	 */
	function moneyFormat($money)
	{
		return ($money < 0 ? "(".number_format(abs($money),0).")" : number_format($money,0));
	}
}

if (! function_exists('abort_if')) {
	
	/**
	 * Display number as money
	 * @param  int    $money 
	 * @return string
	 */
	function abort_if(bool $condition)
	{
		if ($condition) {
			abort(404);
		}
	}
}

if (! function_exists('removeSpaces')) {
	
	/**
	 * Remove all spaces including tabs and new line
	 * @param   $sting 
	 * @return  string
	 */
	function removeSpaces($string)
	{
		return preg_replace('/\s+/', '', $string);
	}
}

if(!function_exists('LoanGuarantorReducer')){

	// If the loan we are paying for has cautionneur, then make sure
	// We update our member cautionneur table by adding the amount
	// paid by this member to the refund amount as long 
	// as cautionneur still have a balance
	function loanGuarantorReducer(Loan $loan,$cautionneurAmount)
	{

		// 1. Get all active loans for this member
		// 2. Calculate payback % per guarantor 
		// 3. Determine what amount guarantor
		// 4. should be added unto based on the %
		
		$activeLoans     = $loan->member->active_loans;
		$totalLoanAmount = $activeLoans->sum('loan_to_repay');

		$activeLoans->each(function($loan) use ($totalLoanAmount,$cautionneurAmount) {
			$loan->cautions->each(function($guarantor) use ($totalLoanAmount,$cautionneurAmount){
				// Only refund for non released guarantors
				if ($guarantor->amount > $guarantor->refunded_amount) {
					// Set percentage to recover for this guarantor
					$amountToRepay =  intval($cautionneurAmount * ($guarantor->amount / $totalLoanAmount));
					// Update amount to repay
	        		if ($amountToRepay > $guarantor->balance) {
	        			$guarantor->refunded_amount  += $guarantor->balance;
	        		}else{
        			   $guarantor->refunded_amount += $amountToRepay;
	        		}

	        		// Save in the db
	        		$guarantor->save();
				}

			});
		});

		return true;
	}
}

/**
 * Export collection to csv
 * @param   $collection 
 * @param   $filename   
 * @return    
 */
function collectionToCsv($collection,$filename)
{
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject);

        $firstRow = $collection->first();
        if (! is_array($firstRow)) {
        	$firstRow = $firstRow->toArray();
        }

        $csv->insertOne(array_keys($firstRow));

        foreach ($collection as $item) {
        	if (! is_array($item)) {
        		$item = $item->toArray();
        	}
            $csv->insertOne($item);
        }

        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ]);
}
/**
 * JSON data to html table
 * 
 * @param object $data
 * 
 */
function jsonToTable ($data)
{
    $table = '<table class="table">';
    
    foreach ($data as $key => $value) {
        $table .= '
        <tr valign="top">
        ';
        if ( ! is_numeric($key)) {
            $table .= '
            <td>
                <strong>'. $key .':</strong>
            </td>
            <td>
            ';
        } else {
            $table .= '
            <td colspan="2">
            ';
        }
        if (is_object($value) || is_array($value)) {
            $table .= jsonToTable($value);
        } else {
            $table .= $value;
        }
        $table .= '
            </td>
        </tr>
        ';
    }
    $table .= '
    </table>
    ';
    return $table;
}
/**
 * Convert HTML to pdf
 * @param  string $html 
 * @return pdf
 */
function htmlToPdf($html)
{
	$options = [
				"encoding"=>"UTF-8",
			    "page-size"=>"A4",
			    "margin-top"=>"0.25in",
			    "margin-right"=>"0.1in",
			    "margin-bottom"=>"0.25in",
			    "margin-left"=>"0.1in",
			    'footer-spacing'=> 2,
				'header-spacing'=> 5,			
			    "disable-smart-shrinking"=> false,
			    ];
	$title = \Request::segment(3);
	if (is_null($title) || empty($title)) {
		$title = 'document.pdf';
	}
    $html = view('partials.report_header')->render() . $html;
	$pdf = App::make('snappy.pdf.wrapper');
	$pdf->loadHTML($html);
	foreach ($options as $key => $value) {
		$pdf->setOption($key, $value);
	}
	// $pdf->setOption('footer-spacing', 2);
	// $pdf->setOption('header-spacing', 5);
	// $pdf->setOption('margin-right', 0);
	// $pdf->setOption('margin-left', 0);
	return $pdf->stream($title);
}
/**
 * htmlentities in PHP but preserving html tags
 * @param  string $htmlString
 * @return string
 */
function htmlentities_keepHtmlTags($htmlString)
{
	$list = get_html_translation_table(HTML_ENTITIES);
	unset($list['"']);
	unset($list['<']);
	unset($list['>']);
	unset($list['&']);
	$search = array_keys($list);
	$values = array_values($list);
	$search = array_map('utf8_encode', $search);
	return str_replace($search, $values, $htmlString);
}
function utf8_encode_deep(&$input) {
    if (is_string($input)) {
        $input = utf8_encode($input);
    } else if (is_array($input)) {
        foreach ($input as &$value) {
            utf8_encode_deep($value);
        }
        unset($value);
    } else if (is_object($input)) {
        $vars = array_keys(get_object_vars($input));
        foreach ($vars as $var) {
            utf8_encode_deep($input->$var);
        }
    }
}
/**
 * Check if a string contains another string or character 
 * @param  string $haystack The string to search in.
 * @param  string $needle   string that should be searched for
 * @return bool           true/false
 */
function stringContains($haystack,$needle){
	return (strpos($haystack, $needle) !== false);
}
/**
 * Get index of a string or a character from another 
 * @param  string $haystack The string to search in.
 * @param  string $needle   string that should be searched for
 * @param  string $offset   If specified, search will start this number of characters counted from the beginning of the string. 
 * If the value is negative, search will instead start from that many characters from the end of the string, searching backwards.
 * @return integer
 */
function stringIndexOf($haystack,$needle,$offset=1)
{
	return strrpos($haystack, $needle, $offset);
}
/**
 * Check if a string start with another string 
 * @param  string $haystack The string to search in.
 * @param  string $needle   string that should be searched for
 * @return bool           true/false
 */
function stringStartsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}
/**
 * Check if a string ends with another string 
 * @param  string $haystack The string to search in.
 * @param  string $needle   string that should be searched for
 * @return bool           true/false
 */
function stringEndsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}
/**
 * Get string between two string 
 * @param  string $haystack The string to search in.
 * @param  string $start    start string
 * @param  string $end      end string string
 * @return string
 */
function stringBetween($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0){
    	return '';
    } 
    $ini += strlen($start);
	
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
	/**
	 * Export to Excel
	 * 
	 * @param  $report 
	 * @param  string $name   
	 * @return file         
	 */
    function toExcel($report,$name='report'){
    		
			$filename = $name .'.xls';
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			echo $report;
	}
/**
 * This will execute $cmd in the background (no cmd window) without PHP waiting for it to finish, on both Windows and Unix. 
 * @param  string $cmd 
 * @return void
 */
function execInBackground($cmd) { 
    if (substr(php_uname(), 0, 7) == "Windows"){ 
        pclose(popen("start /B ". $cmd, "r"));  
    } 
    else { 
        exec($cmd . " > /dev/null &");   
    } 
} 
/**
 * Get simple range dates
 * @return  
 */
function get_simple_date_ranges()
{
		$today =  date('Y-m-d');
		$yesterday = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$six_days_ago = date('Y-m-d', mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		$start_of_this_month = date('Y-m-d', mktime(0,0,0,date("m"),1,date("Y")));
		$end_of_this_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
		$start_of_last_month = date('Y-m-d', mktime(0,0,0,date("m")-1,1,date("Y")));
		$end_of_last_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime((date('m') - 1).'/01/'.date('Y').' 00:00:00'))));
		$start_of_this_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")));
		$end_of_this_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")));
		$start_of_last_year =  date('Y-m-d', mktime(0,0,0,1,1,date("Y")-1));
		$end_of_last_year =  date('Y-m-d', mktime(0,0,0,12,31,date("Y")-1));
		$start_of_time =  date('Y-m-d', 0);
		return array(
			$today. '/' . $today 								=> trans('report.reports_today'),
			$yesterday. '/' . $yesterday						=> trans('report.reports_yesterday'),
			$six_days_ago. '/' . $today 						=> trans('report.reports_last_7'),
			$start_of_this_month . '/' . $end_of_this_month		=> trans('report.reports_this_month'),
			$start_of_last_month . '/' . $end_of_last_month		=> trans('report.reports_last_month'),
			$start_of_this_year . '/' . $end_of_this_year	 	=> trans('report.reports_this_year'),
			$start_of_last_year . '/' . $end_of_last_year		=> trans('report.reports_last_year'),
			$start_of_time . '/' . 	$today						=> trans('report.reports_all_time'),
		);
}
/**
 * Get months
 * @return months as array
 */
function get_months()
{
	$months = array();
	for($k=1;$k<=12;$k++)
	{
		$cur_month = mktime(0, 0, 0, $k, 1, 2000);
		$months[date("m", $cur_month)] = date("M",$cur_month);
	}
	
	return $months;
}
/**
 * Get days
 * @return  days as array
 */
function get_days()
{
	$days = array();
	
	for($k=1;$k<=31;$k++)
	{
		$cur_day = mktime(0, 0, 0, 1, $k, 2000);
		$days[date('d',$cur_day)] = date('j',$cur_day);
	}
	
	return $days;
}
/**
 * Get yeards 
 * @return years as array
 */
function get_years()
{
	$years = array();
	for($k=0;$k<10;$k++)
	{
		$years[date("Y")-$k] = date("Y")-$k;
	}
	
	return $years;
}
/**
 * Get random colors
 * @param   $how_many 
 * @return            
 */
function get_random_colors($how_many)
{
	$colors = array();
	
	for($k=0;$k<$how_many;$k++)
	{
		$colors[] = '#'.random_color();
	}
	
	return $colors;
}
/**
 * Get random color
 * @return  
 */
function random_color()
{
    mt_srand((double)microtime()*1000000);
    $c = '';
    while(strlen($c)<6){
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}
/**
 * Calcuate interest loan interest
 *
 * @param numeric amount
 * @param numeric rate
 * @param integer installments
 *
 * @return float
 */
function calculateInterest($amount,$rate,$installments)
{
		// Interest formular
		// =================
		// The formular to calculate interests at ceb is as following
		// I =  P *(TI * N)
		//     ------------
		//     1200 + (TI*N)
		//
		// Where :   I : Interest
		//           P : Amount to Repay
		//           TI: Interest Rate
		//           N : Montly payment
		$rate_installments = $rate * $installments;
	return ($amount * $rate_installments ) / (1200 + $rate_installments);
}
/**
 * This method helps to generate a written contrat
 * @param  Ceb\Models\User $member who has the contratc
 * @param  string $contract_type  type of the contrac that helps to know which letter to use (social_loan,special_loan,ordinary_loan)
 * @return rendered view
 */
function generateContract($member,$contract_type)
{
	$loan   = $member->latestLoanWithEmergency();
		
	if ($loan->is_regulation) {
		$contract_type = $loan->regulation_type;
	}
	$transactionid =  $loan->transactionid;
	switch ($contract_type) {
			case (strpos($contract_type,'ordinary_loan') !== FALSE):
			     // Ordinary loan
				 $contract = view('reports.contracts_loan_ordinary',compact('transactionid'))->render();
				break;
			case 'special_loan':
			    $loan->monthly_fees = $loan->calculated_monthly_fee;
				// Special loan	
			    $contract = view('reports.contracts_loan_special', compact('transactionid'))->render();
				break;
			case 'social_loan':
				$loan->monthly_fees = $loan->calculated_monthly_fee;
				// Social loan.			
			    $contract = view('reports.contracts_loan_social', compact('transactionid'))->render();
				break;
			case 'installments':
				// Regularisation installments
				$contract = view('reports.contracts_regularisation_installment',compact('transactionid'))->render();
				break;
			case 'amount':
				// Regularisation installments
				$contract = view('reports.contracts_regularisation_amount',compact('transactionid'))->render();
				break;
			case 'amount_installments':
				// Regularisation installments
				$contract = view('reports.contracts_regularisation_amount_installments',compact('transactionid'))->render();
				break;
			case 'emergency_loan':
				// Regularisation installments
				$contract = view('reports.contracts_loan_ordinary',compact('transactionid'))->render();
				break;
			default: // Could not detect the contract
				$contract = 'Unable to determine the contract type';
				break;
		}
		$contract = str_replace('{contract_id}',$loan->loan_contract,$contract);
		$contract = str_replace('{names}',$member->names,$contract);
		$contract = str_replace('{adhersion_id}',$member->adhersion_id,$contract);
		$contract = str_replace('{member_nid}',$member->member_nid,$contract);
		$contract = str_replace('{district}',$member->district,$contract);
		$contract = str_replace('{province}',$member->province,$contract);
		$contract = str_replace('{names}',$member->names,$contract);
		$contract = str_replace('{institution_name}',$member->institution_name,$contract);
		$loan_to_repay = (int) $loan->loan_to_repay;
		$contract = str_replace('{loan_to_repay_word}',convert_number_to_words($loan_to_repay),$contract);
		$contract = str_replace('{loan_to_repay}',number_format($loan_to_repay),$contract);
		$contract = str_replace('{cheque_number}',$loan->cheque_number,$contract);
		$tranches =  $loan->tranches_number;
		$letter_date = $loan->letter_date;
		// If the loan is regulated then use the total amount
		if ($loan->is_regulation == 1) {
			$tranches = $member->remaining_tranches;
			$letter_date = $loan->created_at;
		}
		$contract = str_replace('{tranches_number}',$tranches,$contract);
		$contract = str_replace('{monthly_fees}',number_format((int)$loan->monthly_fees),$contract);
		$contract = str_replace('{interests}',number_format((int) $loan->interests),$contract);
		$contract = str_replace('{urgent_loan_interests}',number_format((int)$loan->urgent_loan_interests),$contract);
		$contract = str_replace('{start_payment_month}',$letter_date->addMonth(1)->format('m-Y'),$contract);
		// We need to remove 1 month that we have added in the starting date
		$contract = str_replace('{end_payment_month}',$letter_date->addMonth($tranches-1)->format('m-Y'),$contract); 
		$contract = str_replace('{tranches_number}',$tranches,$contract);
		$contract = str_replace('{president}',(new Ceb\Models\Setting)->get('general.president'),$contract);	
		$contract = str_replace('{treasurer}',(new Ceb\Models\Setting)->get('general.tresorien'),$contract);	
		$contract = str_replace('{administrator}',(new Ceb\Models\Setting)->get('general.administrator'),$contract);
		
		$cautionnairesTable = view('reports.cautionneurs',compact('loan'))->render();
		
		$contract = str_replace('{cautionnaires_table}',$cautionnairesTable,$contract);
	    $contract = str_replace('{today_date}',date('d-m-Y'),$contract);
	return $contract;	
}
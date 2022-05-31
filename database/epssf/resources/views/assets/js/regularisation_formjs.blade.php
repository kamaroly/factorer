jQuery(document).ready(function($) {

	 // Global variables
 	var data = {};
	/** START BY CALCULATING LOAN DETAILS */
	calculateLoanDetails();

  	/** SET Operation type if it is changed */
  	$("#operation_type").change(function(event) {
  		var loanType = $(this);
  		window.location.href = window.location.protocol+'//'+window.location.host+'/regularisation'+'?operation_type='+loanType.val();
  	});
  	
  	// MAKE SURE THERE IS NO MORE THAN 72 MONTHS ON REPAYMENTS
  	$("#js-complete-button").click(function(event) {
	  	if ((Math.round($('#additional_installments').val().replace(/,/g,'')) + Math.round($('.remaining_tranches').val().replace(/,/g,''))) > 72) {
			alert('You cannot have more than 72 months, Please change installments and try again.');
			event.preventDefault();
			return;
	  	}
  	});


	function calculateLoanDetails(){
		// Remove any character that is not a number
		var additional_amount			= 0;
		var loanBalance					= 0;
		var additional_installments		= 0;
		var remaining_installments		= 0;
		var totalContributions			= 0;
		var additinal_charges_rate		= 0;
		var additinal_charges			= 0;
		var remaining_interest			= 0;
		var totalInstallement_interests	= 0;
		var interest_on_installements	= 0;
		var interest_on_amount 			= 0;
		var interests_to_pay			= 0; 
		var total_interests 			= 0;
		var new_monthly_fees 			= 0;
		var netToReceive 				= 0;
		var amount_bonded				= 0;
		var previous_loan_balance       = 0;
		var operation_type				= $('#operation_type').val();
		
		if(typeof $('#additional_amount').val() !=='undefined'){
	        additional_amount =$('#additional_amount').val().replace(/,/g,''); 
	    }
		
		if(typeof $('#previous_loan_balance').val() !=='undefined'){
	        previous_loan_balance = $('#previous_loan_balance').val().replace(/,/g,'');
	        loanBalance = previous_loan_balance = parseInt(previous_loan_balance);
	    }

		if(typeof $('#additional_installments').val() !=='undefined'){
	        additional_installments =$('#additional_installments').val();
	    }
		
		if(typeof $('.remaining_tranches').val() !=='undefined'){
	        remaining_installments = $('.remaining_tranches').val().replace(/,/g,'');
	        remaining_installments = parseInt(remaining_installments);
	    }
		// Remove any character that is not a number
		if(typeof $('#totalContributions').val() !=='undefined'){
	        totalContributions = $('#totalContributions').val().replace(/,/g,''); 
	        totalContributions = parseInt(totalContributions);
	    }
		
		if(typeof $('.additinal_charges_rate').val() !=='undefined'){
	        additinal_charges_rate =$('.additinal_charges_rate').val().replace(/,/g,''); 
	    }

	    additional_amount 		= parseInt(additional_amount);
		loanToRepay				= parseInt(additional_amount);
		numberOfInstallment		= parseInt(additional_installments) + parseInt(remaining_installments);
		additinal_charges_rate	= parseInt(additinal_charges_rate);

		// DON'T ALLWO MORE THAN 72 TOTAL INSTALLMENTS 
		if (numberOfInstallment > 72) {
			alert('You cannot have more than 72 months, Please change installments and try again.');
			return;
		}

		// Calculate remaining interest
		interestRate				= getInterestRate(remaining_installments);
		remaining_interest			= Math.round(getInterest(loanBalance,interestRate,remaining_installments));
		interestRate				= getInterestRate(numberOfInstallment);

		switch (operation_type) {
			case "installments": // Do this only when this is regularisation echeance/installments
					totalInstallement_interests	= Math.round(getInterest(loanBalance,interestRate,numberOfInstallment));
					interest_on_installements	= Math.round(totalInstallement_interests - remaining_interest);
					new_monthly_fees			= parseInt(Math.round(loanBalance/numberOfInstallment));
					total_interests				= interest_on_installements;
				break;
			
			case "amount": // Do this only when this is regularisation montant/amount
					loanToRepay        +=parseInt(loanBalance);
					interest_on_amount = Math.round(getInterest(Math.round(loanBalance) + Math.round(additional_amount),interestRate,numberOfInstallment));
					interest_on_amount = Math.round(interest_on_amount - remaining_interest);
					if (additinal_charges_rate > 0) {
						additinal_charges	= Math.round((additional_amount * additinal_charges_rate) /  100);
					};

					total_interests		= Math.round(interest_on_amount );
					netToReceive		= additional_amount - interest_on_amount- additinal_charges;
					new_monthly_fees	= parseInt(Math.round((Math.round(loanBalance)+Math.round(additional_amount))/Math.round(numberOfInstallment)));
				break;
			case "amount_installments": // Do this only when this is regularisation montant/amount  and installment
					// CALCULATE AMOUNTS
					interestRate 				= getInterestRate(numberOfInstallment);
					totalInstallement_interests	= Math.round(getInterest(loanBalance,interestRate,numberOfInstallment));
					interest_on_installements	= Math.round(totalInstallement_interests - remaining_interest);
					new_monthly_fees			= parseInt(Math.round(loanBalance/numberOfInstallment));
					total_interests				= interest_on_installements;
					
					// CALCULATE INSTALLMENTS
					loanToRepay        +=parseInt(loanBalance);
					interest_on_amount = Math.round(getInterest(Math.round(additional_amount),interestRate,numberOfInstallment));
					//interest_on_amount = Math.round(interest_on_amount - remaining_interest);
					if (additinal_charges_rate > 0) {
						additinal_charges	= Math.round((additional_amount * additinal_charges_rate)/  100);
					};

					total_interests		= total_interests + Math.round(interest_on_amount );
					netToReceive		= additional_amount - total_interests - additinal_charges;
					new_monthly_fees	= parseInt(Math.round((Math.round(loanBalance)+Math.round(additional_amount))/Math.round(numberOfInstallment)));
				break;
		}		

		// Update fields		
		$('#interests_to_pay').val(Math.round(total_interests));
		$('#interest_on_amount').val(Math.round(interest_on_amount));
		$('#interest_on_installements').val(Math.round(interest_on_installements));

		data[$('#interests_to_pay').attr('name')] = $('#interests_to_pay').val();
    	$('#new_monthly_fees').val(new_monthly_fees);
    	$('#remaining_interest').val(Math.round(remaining_interest));

        // If this regularisation has to pay additinal charges, then calculate administration fees
		// And remove it from the net_to_receive
		$('#additinal_charges').val(parseInt(additinal_charges));
		$('#netToReceive').val(parseInt(netToReceive ));
		$('#loanToRepay').val(parseInt(loanToRepay));
		$('#new_installments').val(numberOfInstallment);
  		
  		// If the amount to repay is less or equal to the 
  		// User total contributions then there is no 
  		// need to the caution then hide the form
  		
  		// We calculate bond amount based on the balance between
  		// Savings - Total loan amount and when this is 
  		// Negative then we spilit balance into two
  		// Guarantors
  		var amount_bonded = totalContributions - (previous_loan_balance + additional_amount);
  		console.log(totalContributions,previous_loan_balance,additional_amount);
		$('#cautionForm').css('display', 'none');


		//  The amount sanctioned is equal to the excess not guaranteed by
		//  Saving the borrower shared equally between the two Cautionneurs.
  		if (amount_bonded < 0 
  			&& (operation_type.toLowerCase() == 'amount_installments' || operation_type.toLowerCase() == 'amount') 
  			) {
		 	 // If this person has previous loan, then consider 
		 	 // use new loan amount to repay as the bond since
		 	 // Other guarators has been recorded previously
		 	 if (previous_loan_balance > 0 ) {
			 	 amount_bonded    = additional_amount;	 	 	
		 	 }
  			// As user to provider caustionneur
  			$('#amount_bonded').val(Math.abs(amount_bonded));
  			// We have amount that need to be bound 
  			// display cuation form

  			// Guarantors form is disabled on regularisation as demanded by
  			// changed by Olivier at Juy 23 ,2020 in point #4 option d in below document
  			// https://docs.google.com/document/d/1D8Qhq4P6l8qw01Ib_6hXxdQF9XBBokf3-fhpPSlFfzs/edit?usp=sharing
  			 $('#cautionForm').css('display','block');

  		}else if(amount_bonded > 0 ){
			 // We have positive balance, re-compute amount bonded
			 // And use balance as the amount to bond
			 amount_bonded = amount_bonded - loanToRepay;  			
			 // As user to provider caustionneur, If amount bonded is positive
			 // it means this member's contribution can cover security for
			 // regularisation, not guarantor / caustionneur needed, but
			 // If this amount is negative guarantor cautionneur is 
			 // need to cover for updated loan
			 if (amount_bonded < 0) {
 				 $('#amount_bonded').val(Math.abs(amount_bonded));
		  		 // We have amount that need to be bound 
		  		 // display cuation form

				// Guarantors form is disabled on regularisation as demanded by
	   			// Olivier on June 30, 2020 in point #4 option d in below document
	  			// https://docs.google.com/document/d/1D8Qhq4P6l8qw01Ib_6hXxdQF9XBBokf3-fhpPSlFfzs/edit?usp=sharing
		  		// $('#cautionForm').css('display','block');
			 }

			 // There is nothing to be done here, take a cup of tea and 
			 // relax, cautionneur / guarantor is not needed
  		}
  		
  		/** UPDATE ACCOUNTS AMOUNT */
  		if ($('#operation_type').val()=='installments') {
			$('#debit_amount_0').val($('#interests_to_pay').val());
			$('#credit_amounts_0').val($('#interests_to_pay').val());
		};

		if ($('#operation_type').val()=='amount') {
			$('#debit_amount_0').val($('#additional_amount').val());
			$('#credit_amounts_0').val($('#netToReceive').val());
			$('#credit_amounts_1').val($('#interests_to_pay').val());
			$('#credit_amounts_2').val($('#additinal_charges').val());
		};

		if ($('#operation_type').val()=='amount_installments') {
			$('#debit_amount_0').val($('#additional_amount').val());
			$('#debit_amount_2').val($('#additional_amount').val());

			$('#credit_amounts_0').val($('#netToReceive').val());
			$('#credit_amounts_1').val($('#interests_to_pay').val());
			$('#credit_amounts_2').val($('#additinal_charges').val());
		};
	}

	/**
	 * Get loan interests
	 * ===================
	 * @param  amount 
	 * @param  rate         	
	 * @param  installments
	 * @return calculated interests
	 */
	function getInterest (amount,rate,installments) {

		// Interest formular
		// =================
		// The formular to calculate interests at ceb is as following
		// I =  P *(TI * N)
		//     ------------
		//     1200 + (TI*N)
		//
		// Where :   I : Interest 
		//           P : Amount to Repay
		//           TI: Interest Rate
		//           N : Montly payment
		// amount * (rate*installments) / 1200 +(rate*installments)
		amount          = parseFloat(amount);
		rate            = parseFloat(rate);
		installments    = parseInt(installments);
		var numerator   =  amount * rate * installments;
		var denominator =  1200 + ( rate * installments );
		var interests   = numerator  / denominator;
		
		return interests;
	}

	/**
	 * De 1 à 12 mois le taux d’intérêt est de 3.4
	 * De 13 à 24 mois le taux d’intérêt est de 3.6
	 * De 25 à 36 mois le taux d’intérêt est de 4.1
	 * De 37 à 48 mois le taux d’intérêts est de 4.3
	 * @return  {[type]} [description]
	 */
	function getInterestRate(numberOfInstallment){
		@foreach ($loanRates as $loanRate)
			if (numberOfInstallment>={!! $loanRate->start_month !!} && numberOfInstallment<={!! $loanRate->end_month !!}) {return {!! (float) $loanRate->rate !!};};
		@endforeach

		// If we cannot find any rate related to this
		// then return maximum
		return {!! (float) $loanRates->max('rate') !!};
	}

   $('.search-cautionneur').click(function(event) {
    	// Prevent the default event action 
    	event.preventDefault();
    	var cautionneur = $(this).parent().find('input');
    	
    	// Check if this input has at least some data
    	if(cautionneur.val() !== "")
    	{
    		var segments = window.location.pathname.split( '/' );

			window.location.href = window.location.protocol+'//'+window.location.host+'/regularisation/setcautionneur'+'?'+cautionneur.attr('name')+'='+cautionneur.val();		
    		return true;
    	}

    	// If we reach here it means we have nothing to do, just return false
    	return false;
      });


	// Detect if an input has been written in 
	$('.loan-input').keyup(function(event) {
		updateInput($(this));
	});	

	// Detect if an input has been written in 
	$('.loan-select').change(function(event) {
		/* First get current input data */
		var data = {};	
		// Make sure you send request when 
		// We only have something in the input
		if ($(this).val()) {
		    data[$(this).attr('name')] = $(this).val();
			setTimeout(updateField(data), 5000);
		};
		calculateLoanDetails();
	});		

	function updateInput (element) {
		/* First get current input data */
		var fieldName = element.attr('name');
        var fieldValue = element.val();
      
		// Validate if this input is wished amount
		// wished amount should not be higher than right to loan
		if(fieldName == 'wished_amount'){
			isValidWishedAmount();
		}
		// Check this is empty then set it to sezo
		if (fieldValue == "") {
			element.val(0);
		};
		// Make sure you send request when 
		// We only have something in the input
		if (element.val()) {
		    data[fieldName] = element.val();
			setTimeout(updateField(data), 5000);
		};
        
		// Update calculations
		calculateLoanDetails();
	}
   	/**
	 * Update loan input field on the server side
	 * @param    json array data data to be sent to the server
	*/
	function updateField(formData,requestUrl){

		/** first calculate existings fields */
		calculateLoanDetails();
		/** If the requestUrl was not initialized then set default to ajax/loan */
		requestUrl = typeof requestUrl!=='undefined' ? requestUrl :'/ajax/regularisation';

		$.ajax({
			url: requestUrl,
			type: 'GET',
			async: true,	
			data: formData,
		})
		.done(function(data) {

		})
		.fail(function(error) {
			console.log("error "+error);
		})
		.always(function() {
		});			
	}

	$('#regularisationForm').submit(function(event) {
		var netToReceive = parseInt($('#netToReceive').val());
		// We cannot allow negative net to recieve
		if (netToReceive < 1) {
		event.preventDefault();
		errorNotifications = '<div data-alert class="alert alert-error radius">Sorry, this regulation is not possible because net to receive is negative</div>';
                  swal.setDefaults({ confirmButtonColor: '#d9534f' });
                  swal({
                    title:"Unable to validate this regulation",
                    text : errorNotifications,
                    type :"error",
                    html :true
                  });
           return false;
		};
	});
})
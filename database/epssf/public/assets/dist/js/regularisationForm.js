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
		var operation_type				= $('#operation_type').val();
		
		if(typeof $('#additional_amount').val() !=='undefined'){
	        additional_amount =$('#additional_amount').val().replace(/,/g,''); 
	    }
		
		if(typeof $('#loanBalance').val() !=='undefined'){
	        loanBalance =$('#loanBalance').val().replace(/,/g,'');
	        loanBalance = parseInt(loanBalance);
	    }
		
		if(typeof $('#additional_installments').val() !=='undefined'){
	        additional_installments =$('#additional_installments').val();
	    }
		
		if(typeof $('.remaining_tranches').val() !=='undefined'){
	        remaining_installments =$('.remaining_tranches').val().replace(/,/g,'');
	        remaining_installments = parseInt(remaining_installments);
	    }
		// Remove any character that is not a number
		if(typeof $('#totalContributions').val() !=='undefined'){
	        totalContributions =$('#totalContributions').val().replace(/,/g,''); 
	    }
		
		if(typeof $('.additinal_charges_rate').val() !=='undefined'){
	        additinal_charges_rate =$('.additinal_charges_rate').val().replace(/,/g,''); 
	    }

	    additional_amount 		= parseInt(additional_amount);
		loanToRepay				= parseInt(additional_amount);
		numberOfInstallment		= parseInt(additional_installments) + parseInt(remaining_installments);
		additinal_charges_rate	= parseInt(additinal_charges_rate);

		if (operation_type.indexOf("amount") > -1) {
			loanToRepay +=parseInt(loanBalance);
		};

		// Calculate remaining interest
		interestRate				= getInterestRate(remaining_installments);
		remaining_interest			= Math.round(getInterest(loanBalance,interestRate,remaining_installments));
		interestRate				= getInterestRate(numberOfInstallment);

		console.log(interestRate,remaining_installments,loanBalance,remaining_interest,numberOfInstallment);
		// Do this only when this is regularisation echeance/installments
		if (operation_type.indexOf("installment") > -1 && operation_type.indexOf("amount") == -1) {		
			totalInstallement_interests	= Math.round(getInterest(loanBalance,interestRate,numberOfInstallment));
			interest_on_installements	= Math.round(totalInstallement_interests - remaining_interest);
			new_monthly_fees			= parseInt(Math.round(loanBalance/numberOfInstallment));
			total_interests				= interest_on_installements;
		};
		
		
		// Do this only when this is regularisation montant/amount
		if (operation_type.indexOf("amount") > -1 && operation_type.indexOf("installment") == -1) {
			totalInstallement_interests = Math.round(getInterest(Math.round(loanBalance) + Math.round(additional_amount),interestRate,numberOfInstallment));
			interest_on_amount			= Math.round(totalInstallement_interests - remaining_interest);

			console.log(interest_on_amount,totalInstallement_interests,remaining_interest,numberOfInstallment);

			if (additinal_charges_rate > 0) {
				additinal_charges	= Math.round((additional_amount * additinal_charges_rate)/  100);
			};

			total_interests		= Math.round(interest_on_amount );
			netToReceive		= additional_amount - interest_on_amount- additinal_charges;
			new_monthly_fees	= parseInt(Math.round((Math.round(loanBalance)+Math.round(additional_amount))/Math.round(numberOfInstallment)));
			
		}

		// Do this only when this is regularisation montant/amount  and installment
		if (operation_type.indexOf("installment") > -1 && operation_type.indexOf("amount") > -1) {		
			totalInstallement_interests	= Math.round(getInterest(loanBalance,interestRate,numberOfInstallment));
			interest_on_installements	= Math.round(totalInstallement_interests - remaining_interest);
	
			totalInstallement_interests = Math.round(getInterest(Math.round(loanBalance) + Math.round(additional_amount),interestRate,numberOfInstallment));
			interest_on_amount			= Math.round(totalInstallement_interests - remaining_interest);

			if (additinal_charges_rate > 0) {
				additinal_charges	= Math.round((additional_amount * additinal_charges_rate)/  100);
			};

			total_interests		= Math.round(interest_on_amount + interest_on_installements);
			netToReceive		= additional_amount - interest_on_installements - interest_on_amount- additinal_charges;
			new_monthly_fees	= parseInt(Math.round((Math.round(loanBalance)+Math.round(additional_amount))/Math.round(numberOfInstallment)));
		}


		// Update fields		
		$('#interests_to_pay').val(Math.round(total_interests) );

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
  		
  		if(parseInt(additional_amount) <= parseInt(totalContributions)){
  			$('#cautionForm').css('display', 'none');
  		}else{
  			$('#cautionForm').css('display','block');

  			// Since the form is shown, let's check the data we should 
  			// Put in the form
  			 $('#amount_bonded').val(parseInt(loanToRepay - totalContributions));
			// Amount bonded or cautionnee 
			// the amount sanctioned is equal to the excess not guaranteed by
			// saving the borrower shared equally between the two Cautionneurs.
			if($('#amount_bonded').val() == ""){
				$('#amount_bonded').val(parseInt($('#netToReceive').val())/2);
			};

  		}

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
		amount = parseFloat(amount);
		rate   = parseFloat(rate);
		installments = parseInt(installments);
		var numerator =  amount * rate * installments;
		var denominator =  1200 +(rate*installments);
		var interests = numerator  / denominator;
		
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
		console.log('Number of installments',numberOfInstallment);
			if (numberOfInstallment>=1 && numberOfInstallment<=12) {return 3.4;};
			if (numberOfInstallment>=13 && numberOfInstallment<=24) {return 3.6;};
			if (numberOfInstallment>=25 && numberOfInstallment<=36) {return 4.1;};
			if (numberOfInstallment>=37 && numberOfInstallment<=48) {return 4.3;};
			if (numberOfInstallment>=49 && numberOfInstallment<=60) {return 4.8;};
			if (numberOfInstallment>=61 && numberOfInstallment<=72) {return 5;};
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
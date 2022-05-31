<style type="text/css" media="print">
	/** @type {PRINT IN LANDSCAPE}  */
 @media print{@page {size: portrait}}
</style>

<script type="text/javascript">
   window.open("{!! route('piece.disbursed.account',['transactionid'=>$transactionid,'export_excel'=>0]) !!}", "_blank");
  window.focus();
</script>

<h3 style="text-align: center;text-decoration: underline">Eastern Provence Staff Solidality Fund   <p>Loan agreement N° {contract_id}</p></h3>
<p>
Between <strong>{names}</strong>, having adhesion number  <strong>{adhersion_id}</strong><br> Domiciled in the District <strong>{district}</strong>  Province <strong>{province}</strong>. <br>
Member of epssf, firstly; <br>
And the Board of Directors of epssf represented by its President.

<p>
IT IS AGREED THAT:<br/>
<p>
	<strong>Artcle 1.</strong> : Mr /Madame/Mlle<strong>{names}</strong>.Employee of <strong>{institution_name}</strong> Receives a loan of  <strong>{loan_to_repay_word}</strong> Rwanda Francs, (<strong>{loan_to_repay}</strong> Frw) Cheque  N° <strong>{cheque_number}</strong>  Refundable as follows <strong>{monthly_fees}</strong> Rwandan Francs per month with an interest of <strong>{interests}</strong> Francs et urgent loan interest <strong>{urgent_loan_interests}</strong> Rwandan Francs to be retracted from the amount loaned.
</p>

<p>
   <strong>Artcle 2 :</strong> The borrower agrees to pay the installments from the month of  <strong>{start_payment_month}</strong> to <strong>{end_payment_month}</strong>  with a total of <strong>{tranches_number}</strong>. month period.
</p>
<p>
	<strong>Artcle 3 :</strong> The borrower will reimburse the said installments in accordance with the provisions of the Rules of the epssf; guarantees are subject to the same regulations. In addition, the debtor cannot resign from the epssf before expiration of debt.
</p>
<p>
	<strong>Artcle 4 :</strong> In the contract signed by the borrowers, the borrower authorizes the epssf to retract on his/her balance at ..........., Rwamagana (salary, notice, paid leave, etc.) the amount left to pay if he ends his contract with .............., Rwamagana. 
</p>
<p>
	<strong>Artcle 5 :</strong> The loan agreement and repayment authorizes the King Faisal Mutual Fund to block, for his benefit, the accounts of the borrower and the endorser up to the amount owed by the borrower to the epssf 
</p>
<p>
	<strong>Artcle 6 :</strong> Any litigation or dispute arising from the application and interpretation of this contract will be the exclusive jurisdiction of the competent courts of Rwanda in the field.
</p> 


<p>
	<strong>Artcle 7 :</strong> The loan is guaranteed by the savings/deposit: 
</p>

<p>
	<strong>{cautionnaires_table}</strong>
</p>

<p>And signed in the presence of the Member of the Board of Directors</p>
<p>
	<strong>Artcle 8 :</strong>The borrower agrees to replace each cautionary that is unable to continue to cautionate that debt.
</p>
<p>
	<strong>Artcle 9 : </strong> This contract is made in two copies which are distributed between the two contracting parties.
</p>

<p style="text-align: center">Done at Kigali, at <strong>{today_date}</strong>.</p>
<div class="container">
	<div class="left">
	<h4 style="text-decoration: underline">Borrower:</h4>
	Names: <strong>{names}</strong> <br/>
    District : {district}, Province {province} <br/>
	ID N° : {member_nid} <br/>
	Signature: ................................................................<br/>
</div>

<div class="right">
	<h4 style="text-decoration: underline">For the epssf commettee</h4>
	Chair of Board:<strong>{president}</strong><br/>
	Signature: ...................................................................................<br/>
	Treasurer:<strong> {treasurer}</strong> <br/>
	Signature: .................................................................................<br/>
	Vice Chair:<strong>{administrator}</strong><br/>
	Signature: ....................................................................................<br/>.
</div>
</div>
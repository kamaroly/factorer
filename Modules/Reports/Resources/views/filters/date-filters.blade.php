<div class="row">

    <div class="col-sm">
        <strong>Start Date:</strong>
        <input class="date form-control" type="text" name="start_date" value="{{ now()->yesterday()->format('Y-m-d') }}">
    </div>

    <div class="col-sm">
        <strong>End Date:</strong>
        <input class="date form-control" type="text" name="end_date" value="{{ now()->format('Y-m-d') }}">
    </div>

    <div class="col-sm"></div>
    <div class="col-sm"></div>
    <div class="col-sm"></div>
</div>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>

<script type="text/javascript">
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            showOtherMonths: true,
            selectOtherMonths: true,
            autoclose: true,
            changeMonth: true,
            changeYear: true,
            orientation: "bottom left"
        });
</script>

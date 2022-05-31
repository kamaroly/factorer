    <!-- Load Helpers -->
    <script src="{!! url('assets/dist/js/functions.js') !!}"></script>
    <!-- jQuery 2.1.4 -->
    <script src="{!! url('assets/plugins/jQuery/jQuery-2.1.4.min.js') !!}"></script>
    <script src="{!! url('assets/dist/js/config.js') !!}"></script>

    <!-- Bootstrap 3.3.2 JS -->
    <script src="{!! url('assets/bootstrap/js/bootstrap.min.js')  !!}" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="{!! url('assets/plugins/slimScroll/jquery.slimscroll.min.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/dist/js/jquery.numeric.js') !!}"></script>
    <!-- FastClick -->
    <script src="{!! url('assets/plugins/fastclick/fastclick.min.js')!!}" type="text/javascript"></script>

    <!-- libraries -->
    <script src="{!! url('/assets/dist/js/select2.full.js') !!}" type="text/javascript"></script>
    <script src="{!! url('/assets/dist/js/handlebars.js') !!}" type="text/javascript"></script>
    <script src="{!! url('/assets/dist/js/typeahead.js') !!}" type="text/javascript"></script>
    <script src="{!! url('/assets/dist/js/jquery.popdown.js') !!}" type="text/javascript"></script>
    <script src="{!! url('/assets/dist/js/jquery.validate.min.js') !!}" type="text/javascript"></script>
    <script src="{!! url('/assets/dist/js/sweetalert.min.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/dist/js/datepickr.js') !!}" type="text/javascript"></script>

     <!-- CEB App -->
    <script src="{!! url('assets/dist/js/app.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/dist/js/debitCreditAccounts.js') !!}" type="text/javascript"></script>

    <script src="{!! url('assets/datepicker/datepicker.js') !!}" type="text/javascript"></script>
    <script type="text/javascript">
        $('[data-toggle="datepicker"]').datepicker({
              format: 'yyyy-mm-dd'
        });

/**
   * Sort an HTML TABLE IN DOM element
   * @usage 1) add #sortable-table-input id to your input and listen to onkeyup event
               Example: <input  id="sortable-table-input" onkeyup="sortTable()" />
            2) Add #sortable-table id to your HTML table
   * @return 
   */
function sortTable() {
  // Declare variables
  var input, filter, table, tableRow, index, rowTextValue;
  input    = document.getElementById("sortable-table-input");
  filter   = input.value.toUpperCase();
  table    = document.getElementById("sortable-table");
  tableRow = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tableRow.length; i++) {

    // If this row exists then perform search
    if (tableRow[i]) {
        // Get inner text of the table first
        rowTextValue = tableRow[i].textContent || tableRow[i].innerText;
        // If inner text has same text as what is in input then 
        // filter it out
        if (rowTextValue.toUpperCase().indexOf(filter) > -1) {
            tableRow[i].style.display = "";
        } else {
            tableRow[i].style.display = "none";
        }
    }
  }
}
    </script>
    @yield('scripts')

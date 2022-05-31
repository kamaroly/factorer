<!DOCTYPE html>
<html>
  <head>
  
    <meta charset="UTF-8">
    <title>EPSSF| PRINTING</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!-- aaddiing heree piece ddebbuurree priinnt seccttiioonn-->
    <link rel="stylesheet" type="text/css" href="{!! url('/assets/dist/css/report.css') !!}">
    @yield('headercss')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
        {{-- partials.css --}}
    <style type="text/css"  media="print">
          @media print {
            body, html, .invoice,wrapper {
                width: 100%;
                font-size: 14px;
                page-break-after: always;
            }
              table { page-break-after:auto }
              tr    { page-break-inside:avoid; page-break-after:auto }
              td    { page-break-inside:avoid; page-break-after:auto }
              thead { display:table-header-group }
              tfoot { display:table-footer-group }

      }
      @page {
        size: A4;
      }
    </style>
    <meta charset="UTF-8" />
    <style type="text/css">
     p{
            font-size: 16px;
          }
          </style>
  </head>
  <body>
    <div class="wrapper">

      <!-- Main content -->
      <section class="invoice">
        <div class="row no-print">
            <div class="col-xs-12">
           <!--    <button onclick="window.print();" class="btn btn-success pull-right" style="margin-right: 5px;"><i class="fa fa-print"></i> Print</button> -->
           <a href="?pdf=1" target="_blank" class="button-success pure-button" >Print</a>
            </div>
          </div>
        <!-- title row -->
        @include('partials.report_header')
        <br/>
        {!! $report !!}
          <div class="row no-print">
            <div class="col-xs-12">
              <button onclick="window.print();" class="btn btn-success pull-right" style="margin-right: 5px;"><i class="fa fa-print"></i> Print</button>
            </div>
          </div>
        </section><!-- /.content -->
    </div><!-- ./wrapper -->

    <script type="text/javascript" src="http://rawgit.com/MrRio/jsPDF/master/dist/jspdf.debug.js"></script>
  </body>
</html>

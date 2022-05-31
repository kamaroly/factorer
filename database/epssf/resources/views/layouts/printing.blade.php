<!DOCTYPE html>
<html>
  <head>  
    <meta charset="UTF-8">
    <title>EPSSF | PRINTING</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="{{ url('assets/tailwind.min.css') }}">
    <link rel="stylesheet" type="text/css" href="/assets/datepicker/datepicker.css">

    <!-- aaddiing heree piece ddebbuurree priinnt seccttiioonn-->
    <link rel="stylesheet" type="text/css" href="{!! url('/assets/dist/css/report-markdown.css') !!}">
    <style type="text/css"  media="print">
      @media print {
         .hidden-print {
            display: none !important;
          }
      }
     </style>

   @if (in_array(request()->segment(3), ['all-details','alldetails']))
       <style type="text/css">
          body{
            width: auto;
          }
          body .markdown-body {
              padding: 0px;
              border-radius: 3px;
              word-wrap: break-word;
              border: 1px solid #fff;        
          }
       </style>
      @endif
  </head>
  <body>
    <button onclick="window.print();" class="
      bg-green-700 text-green-100 px-3 py-1 shadow-md 
      rounded-sm mt-4 mb-4 hover:text-green-200 
      hidden-print">Print</button>
    <article class="markdown-body">
        <!-- title row -->
        @include('partials.report_header')
        <br/>
        {!! $report !!}
    </article>
    <button onclick="window.print();" class="
      bg-green-700 text-green-100 px-3 py-1 shadow-md 
      rounded-sm mt-4 mb-4 hover:text-green-200 
      hidden-print">Print</button>


    <script src="{!! url('assets/plugins/jQuery/jQuery-2.1.4.min.js') !!}"></script>
    <script src="{!! url('assets/datepicker/datepicker.js') !!}" type="text/javascript"></script>
    <script type="text/javascript">
        $('[data-toggle="datepicker"]').datepicker({
              format: 'yyyy-mm-dd'
        });

        /** Navigate with new dates */
        function filterByDate()
        {
          var startDate = document.querySelector('#js-start-date').value;
          var endDate   = document.querySelector('#js-end-date').value;
          var url       = document.querySelector('#js-report-url').value;

          // Get true URL
          url           = '/'+url.replace('{startDate}', startDate).replace('{endDate}', endDate);
          window.location = url;
        }
    </script>

  </body>
</html>

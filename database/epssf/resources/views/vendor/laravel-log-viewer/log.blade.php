  @extends('layouts.default')
  @section('content_title')
  <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> {{ trans('navigations.logs') }}
  @endsection

  @section('content')
   <link href="{{Url()}}/assets/dist/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 sidebar">
          <div class="list-group">
           <?php 
           $logFiles = [];
           foreach ($files as $file) {
             $logFiles  [base64_encode($file)] = $file;
           }

            ?>
            {!! Form::select('files', $logFiles, Input::get('l', null), ['class'=>'form-control','id'=>'select-file']) !!}

          </div>
        </div>
        <div class="col-sm-9 col-md-10 table-container">
          @if ($logs === null)
            <div>
              Log file >50M, please download it.
            </div>
          @else
          <table id="table-log" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Level</th>
                <th>Date</th>
                <th>Content</th>
              </tr>
            </thead>
            <tbody>

@foreach($logs as $key => $log)
<tr>
  <td class="text-{{{$log['level_class']}}}"><span class="glyphicon glyphicon-{{{$log['level_img']}}}-sign" aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
  <td class="date">{{{$log['date']}}}</td>
  <td class="text">
    @if ($log['stack']) <a class="pull-right expand btn btn-default btn-xs" data-display="stack{{{$key}}}"><span class="glyphicon glyphicon-search"></span></a>@endif
    {{{$log['text']}}}
    @if (isset($log['in_file'])) <br />{{{$log['in_file']}}}@endif
    @if ($log['stack']) <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}</div>@endif
  </td>
</tr>
@endforeach

            </tbody>
          </table>
          @endif
          <div>
            <a href="?dl={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-download-alt"></span> Download file</a>
            -
            <a id="delete-log" href="?del={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-trash"></span> Delete file</a>
          </div>
        </div>
      </div>
    </div>
    @endsection

    @section('scripts')
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <script src="{!! url('assets/dist/js/jquery.dataTables.min.js') !!}" type="text/javascript"></script>
    <script src="{!! url('assets/dist/js/dataTables.bootstrap.js') !!}" type="text/javascript"></script>
    <script>
      $(document).ready(function(){
        $('#table-log').DataTable({
          "order": [ 1, 'desc' ],
          "stateSave": true,
          "stateSaveCallback": function (settings, data) {
            window.localStorage.setItem("datatable", JSON.stringify(data));
          },
          "stateLoadCallback": function (settings) {
            var data = JSON.parse(window.localStorage.getItem("datatable"));
            if (data) data.start = 0;
            return data;
          }
        });
        $('.table-container').on('click', '.expand', function(){
          alert('oka');
          $('#' + $(this).data('display')).toggle();
        });
        $('#delete-log').click(function(){
          return confirm('Are you sure?');
        });

        $('#select-file').change(function(event) {
            window.location = '?l='+$(this).val();
        });
      });
    </script>
@endsection
<?php 
 $notification = Session::get('flash_notification');
 $notification = !empty($notification) ? $notification->first() : null;
?>

@if (!empty($notification))
    @if (Session::has('flash_notification.overlay'))
        @include('flash::modal', ['modalClass' => 'flash-modal', 'title' => Session::get('flash_notification.title'), 'body' => Session::get('flash_notification.message')])
    @else
        <div class="alert alert-{{ $notification->level }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

            {{ $notification->message }}
        </div>
    @endif
@endif

@if (count($errors) > 0)
   <div class="alert alert-danger }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<?php $user = $CebUser->find(\Sentry::getUser()->id);
      
      $notifications      = $user->getNotificationsNotRead($limit = 20, $paginate = null, $order = 'desc');
      $countNotifications = count($user->getNotificationsNotRead());

 ?>
<li class="dropdown notifications-menu">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
    <i class="fa fa-bell-o"></i>
    <span class="label label-warning">{!! $countNotifications; !!}</span>
  </a>
  <ul class="dropdown-menu notifications">
    <li class="header">{{ trans('general.your') }} {!! $countNotifications; !!} {{ trans('general.notifications') }} </li>
    <li>
      <!-- inner menu: contains the actual data -->
      <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;"><ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
      @foreach ($notifications as $notification)
        <li class="@if(!$notification->read) active @endif">
            <a href="{{$notification->url}}">
                <span class="text-content">
                    {{ $notification->text }}
                </span>
                <span class="date">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </a>
        </li>
    @endforeach
      </ul><div class="slimScrollBar" style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 195.122px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>
    </li>
    <li class="footer"><a href="{!! route('notificatons') !!}">{{ trans('general.view_all') }}</a></li>
  </ul>
</li>
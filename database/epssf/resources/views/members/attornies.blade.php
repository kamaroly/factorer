@extends('layouts.popdown')
@section('content')
  <div class="row">
     @each ('attornies.item', $member->attornies, 'attorney', 'members.no-items')
  </div>
@endsection
    
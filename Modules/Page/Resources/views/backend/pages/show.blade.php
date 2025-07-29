@extends('frontend::layouts.master')
@section('content')
<div class="page-title">
        <h4 class="m-0 text-center">{{$page->name}}</h4>
</div>

<div class="section-spacing-bottom">

    <div class="container">
        @if(empty($page->description))
        <div class="text-center">
            <img src="{{ asset('img/NoData.png') }}" alt="No Data" class="img-fluid">
            <p>{{__('messages.no_data_found')}}</p>
        </div>
    @else
        <p>{!! $page->description !!}</p>
    @endif
    </div>
</div>

@endsection

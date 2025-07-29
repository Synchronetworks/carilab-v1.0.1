@extends('subscriptions::layouts.master')

@section('content')
    <h1>{{ __('messages.hello_world') }}</h1>

    <p>
    {{ __('messages.view_loaded') }}: {!! config('subscriptions.name') !!}
    </p>
@endsection

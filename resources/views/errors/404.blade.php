@extends('backend.layouts.app')

@section('content')
<div class="error-page" style="background-image: url('{{ asset('default-image/404-background.jpg') }}')">
<div class="container error-page-container">
    <div class="row no-gutters height-self-center flex-column justify-content-center error-page-row">
       <div class="col-sm-12 text-center align-self-center">
          <div class="iq-error position-relative my-5">
                <img src="{{ asset('default-image/404.png') }}" class="img-fluid iq-error-img iq-error-img-dark mx-auto" alt="">
                <h2 class="mb-0 mt-4">{{__('messages.page_not_found')}}</h2>
                <p>{{__('messages.page_not_exist')}}</p>
                <a class="btn btn-primary d-inline-flex align-items-center mt-3" href="{{route('admin-login')}}"><i class="ri-home-4-line"></i>{{__('messages.back_to_home')}}</a>
          </div>
       </div>
    </div>
</div>
</div>

@endsection

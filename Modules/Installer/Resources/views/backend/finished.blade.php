@extends('installer::backend.layouts.master')

@section('template_title')
    {{ trans('installer_messages.final.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.final.title') }}
@endsection


@section('container')

<h6 class="setup-title">{{__('messages.admin_login_credentials')}}</h6>
<p class="text-center">{{__('messages.lbl_email')}}: <b>admin@kivilab.com</b></p>
<p class="text-center">{{__('messages.lbl_password')}}: <b>12345678</b></p>

 <div class="setup-actions">

  

    <div class="buttons">
        <a href="{{ route('admin-login') }}" class="button intsaller-btn" id="exit-admin-button" onclick="disableAdminButton()">
            {{ trans('installer_messages.final.admin_panel') }}
        </a>
    </div>

 </div>

    <script>
      

        function  disableAdminButton() {

           document.getElementById('exit-admin-button').classList.add('disabled');
           document.getElementById('exit-admin-button').innerText = "{{ __('messages.loading') }}";
        }




    </script>


@endsection

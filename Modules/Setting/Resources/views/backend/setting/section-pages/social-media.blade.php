@extends('setting::backend.setting.index')

@section('settings-content')
<form method="POST" action="{{ route('backend.setting.store') }}">
    @csrf
     <div class="card">
        <div class="card-header p-0 mb-4">
            <h4><i class="ph ph-user-list"></i> {{ __('messages.lbl_social-media') }} </h4>
        </div>

        <div class="card-body p-0">
<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            {{ html()->label(__('messages.facebook_url'))->class('col-sm-6 form-label')->for('facebook_url') }}
            <div class="col-sm-12">
                {{ html()->text('facebook_url', $socialmedia['facebook_url'] ?? '')->class('form-control')->placeholder(__('messages.facebook_url_placeholder')) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-label">{{ __('messages.twitter_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('twitter_url', $socialmedia['twitter_url'] ?? '')->class('form-control')->placeholder(__('messages.twitter_url_placeholder')) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-label">{{ __('messages.linkedin_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('linkedin_url', $socialmedia['linkedin_url'] ?? '')->class('form-control')->placeholder(__('messages.linkedin_url_placeholder')) }}
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
            <label for="" class="col-sm-6 form-label">{{ __('messages.instagram_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('instagram_url', $socialmedia['instagram_url'] ?? '')->class('form-control')->placeholder(__('messages.instagram_url_placeholder')) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-label">{{ __('messages.youtube_url') }}</label>
            <div class="col-sm-12">
                {{ html()->text('youtube_url', $socialmedia['youtube_url'] ?? '')->class('form-control')->placeholder(__('messages.youtube_url_placeholder')) }}
            </div>
        </div>
    </div>

  
            <div class="text-end mt-3">
                {{ html()->button(__('messages.save'))
                    ->type('submit')
                    ->class('btn btn-primary') }}
            </div>
     
</div>
        </div>
     </div>
</form>
@endsection





@extends('setting::backend.setting.index')

@section('settings-content')
{{ html()->form('POST', route('backend.setting.store'))
    ->attribute('data-toggle', 'validator')
    ->attribute('id', 'form-submit')  // Add the id attribute here
    ->class('requires-validation')  // Add the requires-validation class
    ->attribute('novalidate', 'novalidate')  // Disable default browser validation
    ->attribute('enctype', 'multipart/form-data')
    ->open()
}}
    @csrf
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>   <i class="ph ph-cube"></i> {{ __('messages.lbl_General') }}</h4>
      <button type="button" class="btn btn-primary float-right" onclick="clearCache()">
        <i class="fa-solid fa-arrow-rotate-left mx-2"></i>{{ __('messages.purge_cache') }}
      </button>
    </div>
    <div class="row gy-4">
      <div class="form-group">
        <label class="form-label">{{ __('messages.lbl_app') }} <span class="text-danger">*</span></label>
        {{ html()->text('app_name')
                  ->class('form-control')
                  ->value($data['app_name'] ?? old('app_name'))
                  ->required() }}
                  <div class="invalid-feedback" id="name-error">{{ __('messages.app_required') }}</div>
      </div>
  
      <div class="form-group">
        <label class="form-label">{{ __('messages.lbl_user_app') }} <span class="text-danger">*</span></label>
        {{ html()->text('user_app_name')
                  ->class('form-control')
                  ->value($data['user_app_name'] ?? old('user_app_name'))
                  ->required() }}
                  <div class="invalid-feedback" id="name-error">{{ __('messages.user_app_required') }}</div>
      </div>
  
      <div class="form-group">
        <label class="form-label">{{ __('messages.lbl_contact_no') }} <span class="text-danger">*</span></label>
        {{ html()->text('helpline_number')
                  ->class('form-control')
                  ->value($data['helpline_number'] ?? old('helpline_number'))
                  ->required() }}
                  <div class="invalid-feedback" id="name-error">{{ __('messages.helpline_number_required') }}</div>
      </div>
  
      <div class="form-group">
        <label class="form-label">{{ __('messages.lbl_inquiry_email') }} <span class="text-danger">*</span></label>
        {{ html()->email('inquriy_email')
                  ->class('form-control')
                  ->value($data['inquriy_email'] ?? old('inquriy_email'))
                  ->required() }}
                  <div class="invalid-feedback" id="name-error">{{ __('messages.inquiry_email_required') }}</div>
      </div>
  
      <div class="form-group">
        <label class="form-label">{{ __('messages.lbl_site_description') }} <span class="text-danger">*</span></label>
        {{ html()->text('short_description')
                  ->class('form-control')
                  ->value($data['short_description'] ?? old('short_description'))
                  ->required() }}
                  <div class="invalid-feedback" id="name-error">{{ __('messages.short_description_required') }}</div>
      </div>

      <div class="form-group col-lg-6">
        <label for="logo" class="form-label">{{ __('messages.logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center">
              <div class="card-body">
               
                <img id="logoViewer" src={{ $data['logo'] ?? asset('img/logo/logo.png') }} class="img-fluid" alt="logo" />
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" class="form-control d-none" id="logo" name="logo" accept=".jpeg, .jpg, .png, .gif">
              <button type="button" class="btn btn-primary mb-5">{{ __('messages.upload') }}</button>
              <button type="button" class="btn btn-secondary mb-5" id="removeLogoButton">{{ __('messages.remove') }}</button>
            </div>
            <span class="text-danger" id="error_logo"></span>
          </div>
        </div>
      </div>

      <!-- Mini Logo Upload -->
      <div class="form-group col-lg-6">
        <label for="mini_logo" class="form-label">{{ __('messages.mini_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center">
              <div class="card-body">

                  <img id="miniLogoViewer"  src={{ $data['mini_logo'] ?? asset('img/logo/mini_logo.png')  }} class="img-fluid" alt="mini_logo" />
               
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" class="form-control d-none" id="mini_logo" name="mini_logo" accept=".jpeg, .jpg, .png, .gif">
              <button type="button" class="btn btn-primary mb-5">{{ __('messages.upload') }}</button>
              <button type="button" class="btn btn-secondary mb-5" id="removeMiniLogoButton">{{ __('messages.remove') }}</button>
            </div>
            <span class="text-danger" id="error_mini_logo"></span>
          </div>
        </div>
      </div>

      <!-- Dark Logo Upload -->
      <div class="form-group col-lg-6">
        <label for="dark_logo" class="form-label">{{ __('messages.dark_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center bg-dark">
              <div class="card-body">
                  <img id="darkLogoViewer"  src={{ $data['dark_logo'] ?? asset('img/logo/dark_logo.png')  }} class="img-fluid" alt="dark_logo" />
               
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" class="form-control d-none" id="dark_logo" name="dark_logo" accept=".jpeg, .jpg, .png, .gif">
              <button type="button" class="btn btn-primary mb-5">{{ __('messages.upload') }}</button>
              <button type="button" class="btn btn-secondary mb-5" id="removeDarkLogoButton">{{ __('messages.remove') }}</button>
            </div>
            <span class="text-danger" id="error_dark_logo"></span>
          </div>
        </div>
      </div>

      <!-- Light Logo Upload -->
      <div class="form-group col-lg-6">
        <label for="light_logo" class="form-label">{{ __('messages.light_logo') }}</label>
        <div class="row align-items-center">
          <div class="col-lg-4">
            <div class="card text-center bg-light">
              <div class="card-body">
                  <img id="lightLogoViewer"  src={{ $data['light_logo'] ?? asset('img/logo/logo.png')  }} class="img-fluid" alt="light_logo" />
                
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2">
              <input type="file" class="form-control d-none" id="light_logo" name="light_logo" accept=".jpeg, .jpg, .png, .gif">
              <button type="button" class="btn btn-primary mb-5" >{{ __('messages.upload') }}</button>
              <button type="button" class="btn btn-secondary mb-5" id="removeLightLogoButton">{{ __('messages.remove') }}</button>
            </div>
            <span class="text-danger" id="error_light_logo"></span>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group text-end">
      <button type="submit" class="btn btn-primary float-right" id="submit-button">
     {{ __('messages.save') }}
      </button>
    </div>
  </form>
  @endsection
  @push('after-scripts')

  <script>

$(document).ready(function() {
    // Logo handlers
    const $logo = $('#logo');
    const $logoViewer = $('#logoViewer');
    const $miniLogo = $('#mini_logo');
    const $miniLogoViewer = $('#miniLogoViewer');
    const $darkLogo = $('#dark_logo');
    const $darkLogoViewer = $('#darkLogoViewer');
    const $lightLogo = $('#light_logo');
    const $lightLogoViewer = $('#lightLogoViewer');

    // Upload button handlers
    $('.btn-primary').on('click', function() {
        const targetInput = $(this).prev('input[type="file"]');
        targetInput.trigger('click');
    });

    // Remove button handlers
    $('#removeLogoButton').on('click', function() {
        $logo.val('');
        $logoViewer.attr('src', '{{ asset("img/logo/logo.png") }}');
    });

    $('#removeMiniLogoButton').on('click', function() {
        $miniLogo.val('');
        $miniLogoViewer.attr('src', '{{ asset("img/logo/mini_logo.png") }}');
    });

    $('#removeDarkLogoButton').on('click', function() {
        $darkLogo.val('');
        $darkLogoViewer.attr('src', '{{ asset("img/logo/dark_logo.png") }}');
    });

    $('#removeLightLogoButton').on('click', function() {
        $lightLogo.val('');
        $lightLogoViewer.attr('src', '{{ asset("img/logo/logo.png") }}');
    });

    // File input change handlers
    $logo.on('change', function() {
        previewFile(this, $logoViewer);
    });

    $miniLogo.on('change', function() {
        previewFile(this, $miniLogoViewer);
    });

    $darkLogo.on('change', function() {
        previewFile(this, $darkLogoViewer);
    });

    $lightLogo.on('change', function() {
        previewFile(this, $lightLogoViewer);
    });

    // Helper function to preview files
    function previewFile(input, viewer) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                viewer.attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
});

    function clearCache() {
    Swal.fire({
        title: "{{ __('messages.are_you_sure') }}",
        text: "{{ __('messages.are_you_sure_to_clear_the_cache') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Clear it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('backend.settings.clear-cache') }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        title: "{{ __('messages.success') }}",
                        text: "{{ __('messages.cache_clear_successfully') }}", // Use the dynamic message from the server
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        title: "{{ __('messages.error') }}",
                        text: "{{ __('messages.an_unexpected_error_occurred') }}",
                        icon: 'error',
                        showConfirmButton: true
                    });
                }
            })
            .catch(error => {
                console.error(__('messages.error_clearing_cache'), error);
                Swal.fire({
                    title: "{{ __('messages.error') }}",
                    text: "{{ __('messages.an_error_occurred_while_clearing_the_cache') }}",
                    icon: 'error',
                    showConfirmButton: true
                });
            });
        }
    });
}

  const logoInput = document.getElementById('logo');
  const logoViewer = document.getElementById('logoViewer');


  logoInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        logoViewer.src = e.target.result;
      }
      reader.readAsDataURL(file);
    }
  });


  const minilogoInput = document.getElementById('mini_logo');
  const miniLogoViewer = document.getElementById('miniLogoViewer');

  minilogoInput.addEventListener('change', function() {
    const minilogofile = this.files[0];
    if (minilogofile) {
      const reader = new FileReader();
      reader.onload = function(e) {
        miniLogoViewer.src = e.target.result;
      }
      reader.readAsDataURL(minilogofile);
    }
  });

  const darklogoInput = document.getElementById('dark_logo');
  const darkLogoViewer = document.getElementById('darkLogoViewer');

  darklogoInput.addEventListener('change', function() {
    const darklogofile = this.files[0];
    if (darklogofile) {
      const reader = new FileReader();
      reader.onload = function(e) {
        darkLogoViewer.src = e.target.result;
      }
      reader.readAsDataURL(darklogofile);
    }
  });


  const lightlogoInput = document.getElementById('light_logo');
  const lightLogoViewer = document.getElementById('lightLogoViewer');

  lightlogoInput.addEventListener('change', function() {
    const lightlogofile = this.files[0];
    if (lightlogofile) {
      const reader = new FileReader();
      reader.onload = function(e) {
        lightLogoViewer.src = e.target.result;
      }
      reader.readAsDataURL(lightlogofile);
    }
  });



  </script>
    @endpush


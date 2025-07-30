<?php
$notifications_count = optional(auth()->user())->unreadNotifications->count();
?>
<nav
class="nav navbar navbar-expand-xl navbar-light iq-navbar header-hover-menu left-border {{ !empty(getCustomizationSetting('navbar_show')) ? getCustomizationSetting('navbar_show') : '' }} {{ getCustomizationSetting('header_navbar') }}">
    <div class="container-fluid navbar-inner">
        <a href="{{ route('backend.home') }}" class="navbar-brand">
            <div class="logo-main">
                <div class="logo-mini d-none">
                    <img src="{{ GetSettingValue('mini_logo') ?? asset(setting('mini_logo')) }}" height="30" alt="{{ app_name() }}">
                </div>
                <div class="logo-normal">
                    <img src="{{ GetSettingValue('logo') ?? asset(setting('logo')) }}" height="30" alt="{{ app_name() }}">

                </div>
                @if(request()->header('data-bs-theme') == 'dark')
                <div class="logo-dark">
                    <img src="{{ GetSettingValue('dark_logo') ?? asset(setting('dark_logo')) }}" height="30" alt="{{ app_name() }}">
                </div>
                @endif
            </div>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon d-flex">
                <i class="ph ph-caret-right"></i>
            </i>
        </div>

       <!-- horizontal header code -->

        <div class="d-flex align-items-center">
            <button id="navbar-toggle" class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">
                    <span class="navbar-toggler-bar bar1 mt-1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0 gap-3">
                <li class="nav-item dropdown">
                    <div class="d-flex align-items-center mr-2 iq-font-style" role="group" aria-label="First group">
                        <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-sm"
                            id="font-size-sm" checked>
                        <label for="font-size-sm" class="btn btn-border border-0 btn-icon btn-sm"
                            data-bs-toggle="tooltip" title="Font size 14px" data-bs-placement="bottom">
                            <span class="mb-0 h6" style="color: inherit !important;">{{__('messages.a')}}</span>
                        </label>
                        <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-md"
                            id="font-size-md">
                        <label for="font-size-md" class="btn btn-border border-0 btn-icon" data-bs-toggle="tooltip"
                            title="Font size 16px" data-bs-placement="bottom">
                            <span class="mb-0 h4" style="color: inherit !important;">{{__('messages.a')}}</span>
                        </label>
                        <input type="radio" class="btn-check" name="theme_font_size" value="theme-fs-lg"
                            id="font-size-lg">
                        <label for="font-size-lg" class="btn btn-border border-0 btn-icon" data-bs-toggle="tooltip"
                            title="Font size 18px" data-bs-placement="bottom">
                            <span class="mb-0 h2" style="color: inherit !important;">{{__('messages.a')}}</span>
                        </label>
                    </div>
                </li>
                <li class="nav-item theme-scheme-dropdown dropdown iq-dropdown">
                    <a href="javascript:void(0)" class="nav-link d-flex align-items-center change-mode p-0"
                        data-change-mode="{{ (auth()->user()->user_setting['theme_scheme'] ?? 'light') == 'dark' ? 'light' : 'dark' }}"
                        id="mode-drop" style="color: inherit !important;">
                        <i class="ph ph-sun mode-icons light-mode"></i>
                        <i class="ph ph-moon mode-icons dark-mode"></i>
                    </a>
                </li>
                <li class="nav-item dropdown iq-dropdown">
                    <a class="nav-link btn btn-primary btn-icon btn-sm rounded btn-action p-0 rounded-pill"
                        data-bs-toggle="dropdown" href="#">
                        <div class="iq-sub-card">
                            <div class="d-flex align-items-center notification_list">
                                <span class="btn-inner">
                                    <i class="ph ph-bell"></i>
                                </span>
                                @if ($notifications_count > 0)
                                    <span class="notification-alert">{{ $notifications_count }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    <ul class="p-0 sub-drop dropdown-menu dropdown-menu-end">
                        <div class="m-0 shadow-none card bg-transparent notification_data"></div>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center p-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false"
                        style="color: inherit !important;">

                        <img src="{{ asset('flags/' . App::getLocale() . '.png') }}" alt="flag" class="img-fluid me-2"
                        style="width: 20px; height: auto; min-width: 15px;"
                        onerror="this.onerror=null; this.src='{{asset('flags/globe.png')}}';">
                            {{ strtoupper(App::getLocale()) }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header bg-primary-subtle py-2 rounded">
                            <div class="fw-semibold">{{ __('messages.change_language') }}</div>
                        </div>
                        @foreach (config('app.available_locales') as $locale => $title)
                            <a class="dropdown-item" href="{{ route('language.switch', $locale) }}">
                                <img src="{{ asset('flags/' . $locale . '.png') }}" alt="flag" class="img-fluid mr-2"style="width: 20px;height: auto;min-width: 15px;">
                                   {{ $title }}
                            </a>
                        @endforeach
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link p-0" data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-md">
                            <img class="avatar avatar-40 img-fluid rounded-circle d-block" src="{{ setBaseUrlWithFileName(auth()->user()->profile_image) }}"
                                alt="{{ auth()->user()->name ?? default_user_name() }}" loading="lazy">
                        </div>
                    </a>
                    <ul class="dropdown-menu sub-drop dropdown-menu-end">
                        <div class="dropdown-header bg-primary-subtle py-3 rounded">
                            <div class="d-flex gap-2">
                                <img class="avatar avatar-40 img-fluid rounded-circle d-block"
                                    src="{{ setBaseUrlWithFileName(auth()->user()->profile_image) }}" />
                                <div class="d-flex flex-column align-items-start">
                                    <h5 class="m-0 text-primary">{{ Auth::user()->full_name ?? default_user_name() }}
                                    </h5>
                                    <span class="text-muted">{{ Auth::user()->email ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center"
                                href="{{ route('backend.my-profile') }}">{{ __('messages.myprofile') }}<i class="ph ph-user"></i></a>
                        </li>
                        @if(auth()->user()->hasRole('vendor'))

                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center"
                                href="{{ route('backend.vendors.details', ['id' => auth()->id()]) }}">
                                {{ __('messages.myinfo') }}<i class="ph ph-user"></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('backend.vendors.subscription-history') }}" class="dropdown-item d-flex justify-content-between align-items-center">
                                    <span class="fw-medium">{{__('messages.subscription_history')}}</span>
                                    <i class="ph ph-clock-counter-clockwise"></i>
                            </a>
                        </li>
                        <li>
                            @php
                                $user = auth()->user();
                                $wallet = $user->wallet;
                                $wallet_amount=  $wallet->amount ?? 0;
                            @endphp
                            <span class="dropdown-item d-flex justify-content-between align-items-center">
                                {{ __('messages.wallet') }} <span class="wallet-text">{{Currency::format($wallet_amount)}}</span>
                            </span>
                        </li>
                        @endif
                        @role('admin|demo_admin')
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                    href="{{ route('backend.settings.general') }}">
                                    {{ __('messages.settings') }}<i class="ph ph-gear"></i>
                                </a>
                            </li>
                        @endrole

                        <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center"
    href="{{ route('admin-logout') }}"
    id="logout-link">
    @lang('messages.logout')<i class="ph ph-sign-out"></i>
</a>
                        </li>
                        <form id="logout-form" action="{{ route('admin-logout') }}" method="POST"
                            style="display: none;"> @csrf </form>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

@push('after-scripts')
    <script type="text/javascript">
       $(document).ready(function() {

$('.change-mode').on('click', function() {
    const element = document.querySelector('html');
    let value = element.getAttribute('data-bs-theme');
    sessionStorage.setItem('theme_mode', value !== 'dark' ? 'dark' : 'light');
    if (value !== 'dark') {
        $('html').attr('data-bs-theme','dark');
    } else {
        $('html').attr('data-bs-theme','light');
    }
});

$('input[name="theme_font_size"]').on('change', function() {
    const font = $('[name="theme_font_size"]').map(function() {
        return $(this).attr('value')
    }).get();
    $('html').removeClass(font).addClass($(this).val());
});
$('.notification_list').on('click', function() {
    notificationList();
});

    // Logout handler
    $('#logout-link').on('click', function(e) {
        e.preventDefault();
        const $form = $('#logout-form');

        $form.off('submit');

        $form.on('submit', function(e) {
            e.preventDefault();

            this.submit();
        });

        $form.trigger('submit');
    });

});



        function notification_count() {

            var url = "{{ route('notification.counts') }}";
            $.ajax({
                type: 'get',
                url: url,
                success: function(res) {



                }
            });


        }

        function notificationList(type = '') {
            var url = "{{ route('notification.list') }}";
            $.ajax({
                type: 'get',
                url: url,
                data: {
                    'type': type
                },
                success: function(res) {

                    $('.notification_data').html(res.data);
                    getNotificationCounts();
                    if (res.type == "markas_read") {
                        notificationList();
                    }
                    $('.notify_count').removeClass('notification_tag').text('');
                }
            });
        }
    </script>
@endpush

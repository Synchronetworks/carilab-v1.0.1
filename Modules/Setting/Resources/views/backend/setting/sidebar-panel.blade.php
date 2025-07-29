
<div class="col-md-4 col-lg-3">
    <div id="setting-sidebar" class="setting-sidebar-inner">
        <div class="card">
            <div class="card-body">
                <div class="list-group list-group-flush" id="setting-list">
                    @hasPermission('setting_bussiness')
                        <div class="mb-3 active-menu">
                            <a id="link-general" href="{{ route('backend.settings.general') }}" class="btn btn-border {{ request()->routeIs('backend.settings.general') ? 'active' : '' }}">
                                <i class="ph ph-cube"></i>{{ __('messages.lbl_General') }}
                            </a>
                        </div>
                    @endhasPermission
                    @hasPermission('setting_custom_code')
                        <div class="mb-3 active-menu">
                            <a id="link-custom-code" href="{{ route('backend.settings.custom-code') }}" class="btn btn-border {{ request()->routeIs('backend.settings.custom-code') ? 'active' : '' }}">
                                <i class="ph ph-barcode"></i>{{ __('messages.lbl_custom_code') }}
                            </a>
                        </div>
                    @endhasPermission
                  
                    @hasPermission('setting_misc')
                        <div class="mb-3 active-menu">
                            <a id="link-misc" href="{{ route('backend.settings.misc') }}" class="btn btn-border {{ request()->routeIs('backend.settings.misc') ? 'active' : '' }}">
                                <i class="ph ph-faders-horizontal"></i>{{ __('messages.lbl_misc_setting') }}
                            </a>
                        </div>
                    @endhasPermission
                 
                 
                    @hasPermission('setting_customization')
                        <div class="mb-3 active-menu">
                            <a id="link-customization" href="{{ route('backend.settings.customization') }}" class="btn btn-border {{ request()->routeIs('backend.settings.customization') ? 'active' : '' }}">
                                <i class="ph ph-user-list"></i>{{ __('messages.lbl_customization') }}
                            </a>
                        </div>
                    @endhasPermission
                    @hasPermission('setting_mail')
                        <div class="mb-3 active-menu">
                            <a id="link-mail" href="{{ route('backend.settings.mail') }}" class="btn btn-border {{ request()->routeIs('backend.settings.mail') ? 'active' : '' }}">
                                <i class="ph ph-envelope-simple"></i>{{ __('messages.lbl_mail') }}
                            </a>
                        </div>
                    @endhasPermission
                    @hasPermission('setting_notification')
                        <div class="mb-3 active-menu">
                            <a id="link-notification" href="{{ route('backend.settings.notificationsetting') }}" class="btn btn-border {{ request()->routeIs('backend.settings.notificationsetting') ? 'active' : '' }}">
                                <i class="ph ph-megaphone"></i>{{ __('messages.lbl_notification') }}
                            </a>
                        </div>
                    @endhasPermission
                    <div class="mb-3 active-menu">
                        <a id="link-payment-method" href="{{ route('backend.settings.payment-method') }}" class="btn btn-border {{ request()->routeIs('backend.settings.payment-method') ? 'active' : '' }}">
                            <i class="ph ph-coin-vertical"></i>{{ __('messages.lbl_payment') }}
                        </a>
                    </div>
                    @hasPermission('setting_language')
                        <div class="mb-3 active-menu">
                            <a id="link-language-settings" href="{{ route('backend.settings.language-settings') }}" class="btn btn-border {{ request()->routeIs('backend.settings.language-settings') ? 'active' : '' }}">
                                <i class="ph ph-translate" aria-hidden="true"></i>{{ __('messages.lbl_language') }}
                            </a>
                        </div>
                    @endhasPermission
                   
                  
                    @hasPermission('other-settings')
                    <div class="mb-3 active-menu">
                        <a id="link-other-settings" href="{{ route('backend.settings.other-settings') }}" class="btn btn-border {{ request()->routeIs('backend.settings.other-settings') ? 'active' : '' }}">
                            <i class="ph ph-squares-four"></i>{{ __('messages.app_configuration') }}
                        </a>
                    </div>
                    @endhasPermission
                    @hasPermission('commission-settings')
                    <div class="mb-3 active-menu">
                        <a id="link-commission-setting" href="{{ route('backend.settings.commission-setting') }}" class="btn btn-border {{ request()->routeIs('backend.settings.commission-setting') ? 'active' : '' }}">
                            <i class="ph ph-piggy-bank"></i>{{ __('messages.commision_settings') }}
                        </a>
                    </div>
                    @endhasPermission
                    <div class="mb-3 active-menu">
                        <a id="link-commission-setting" href="{{ route('backend.settings.import-data') }}" class="btn btn-border {{ request()->routeIs('backend.settings.import-data') ? 'active' : '' }}">
                            <i class="ph ph-tree-structure"></i>{{ __('messages.import_data_setting') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function toggle() {
            const formOffcanvas = document.getElementById('offcanvas');
            formOffcanvas.classList.add('show');
        }

        function hasPermission(permission) {
            return window.auth_permissions.includes(permission);
        }
    </script>
@endpush

<style scoped>
    .btn-border {
        text-align: left;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
</style>

<?php

namespace App\Http\Middleware;

use App\Trait\Menu;
use App\Models\User;

class GenerateMenus
{
    use Menu;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle()
    {
        return \Menu::make('menu', function ($menu) {
            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin') || auth()->user()->hasRole('vendor')) {
                $this->staticMenu($menu, ['title' => __('messages.main'), 'order' => 0]);
                $this->mainRoute($menu, [
                    'icon' => 'ph ph-squares-four',
                    'title' => __('messages.dashboard'),
                    'route' => 'backend.home',
                    'active' => ['app', 'app/dashboard'],
                    'order' => 0,
                ]);
            }
            $permissionsToCheck = ['view_bookings'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.booking_management'), 'order' => 0]);
            }


            $this->mainRoute($menu, [
                'icon' => 'ph ph-list-checks',
                'title' => __('messages.appointments'),
                'route' => 'backend.appointments.index',
                'active' => 'app/appointments',
                'nickname' => 'appointments',
                'shortTitle' => 'b',
                'order' => 0,
                'permission' => ['view_bookings'],
            ]);

           

            $permissionsToCheck = ['view_vendor','view_collector','view_user','view_customer','view_vendordocuments','view_collectordocuments','view_commision','view_collector_bank','view_vendor_bank'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.user_management'), 'order' => 0]);
            }
        if(multiVendor() == "1"){


            $vendors = $this->parentMenu($menu, [
                'icon' => 'ph ph-user-check',
                'title' => __('messages.vendors'),
                'nickname' => 'vendors',
                'permission' => ['view_vendor'],
                'order' => 0,
            ]);

            $this->childMain($vendors, [
                'icon' => 'ph ph-user-check',
                'title' => __('messages.vendor_list'),
                'route' => 'backend.vendors.index',
                'active' => 'app/vendors',
                'permission' => ['view_vendor'],
                'order' => 0,
            ]);
            $this->childMain($vendors, [
                'icon' => 'ph ph-identification-card',
                'title' => __('messages.pending_vendor_list'),
                'route' => ['backend.vendors.index_list','pending'],
                'active' => 'app/vendors/list/pending',
                'permission' => ['view_vendor'],
                'order' => 0,
            ]);

            $this->childMain($vendors, [
                'icon' => 'ph ph-folder-user',
                'title' => __('messages.vendor_document_list'),
                'route' => 'backend.vendordocument.index',
                'active' => 'app/vendordocument',
                'permission' => ['view_vendordocuments'],
                'order' => 0,
            ]);
        
            $this->childMain($vendors, [
                'icon' => 'ph ph-building',
                'title' => __('messages.vendor_banks'),
                'route' => 'backend.vendor_bank.index',
                'active' => ['app/vendor_bank'],
                'permission' => ['view_vendor_bank'],
                'order' => 0,
            ]);

            if(Setting('vendor_commission_type')=='global'){
                $this->childMain($vendors, [
                    'icon' => 'ph ph-list',
                    'title' => __('messages.vendor_commisions'),
                    'route' => 'backend.vendor_commisions.index',
                    'active' => ['app/vendor_commisions'],
                    'permission' => ['view_vendor_commisions'],
                    'order' => 0,
                ]);
            }
        }
            $collectors = $this->parentMenu($menu, [
                'icon' => 'ph ph-user-circle-check',
                'title' => __('messages.collectors'),
                'nickname' => 'collectors',
                'permission' => ['view_collector'],
                'order' => 0,
            ]);

            $this->childMain($collectors, [
                'icon' => 'ph ph-user-circle-check',
                'title' => __('messages.collector_list'),
                'route' => 'backend.collectors.index',
                'active' => 'app/collectors',
                'permission' => ['view_collector'],
                'order' => 0,
            ]);
            $this->childMain($collectors, [
                'icon' => 'ph ph-identification-badge',
                'title' => __('messages.pending_collector_list'),
                'route' => ['backend.collectors.index_list','pending'],
                'active' => 'app/collectors/list/pending',
                'permission' => ['view_collector'],
                'order' => 0,
            ]);
            $this->childMain($collectors, [
                'icon' => 'ph ph-question',
                'title' => __('messages.unassigned_collector_list'),
                'route' => ['backend.collectors.index_list','unassigned'],
                'active' => 'app/collectors/list/unassigned',
                'permission' => ['view_collector'],
                'order' => 0,
            ]);

            $this->childMain($collectors, [
                'icon' => 'ph ph-folder-user',
                'title' => __('messages.collector_document_list'),
                'route' => 'backend.collectordocument.index',
                'active' => 'app/collectordocument',
                'permission' => ['view_collectordocuments'],
                'order' => 0,
            ]);

            if(Setting('collector_commission_type')=='global'){
                $this->childMain($collectors, [
                    'icon' => 'ph ph-list',
                    'title' => __('messages.collector_commisions'),
                    'route' => 'backend.collector_commisions.index',
                    'active' => ['app/collector_commisions'],
                    'permission' => ['view_collector_commisions'],
                    'order' => 0,
                ]);
    
                }
                $this->childMain($collectors, [
                    'icon' => 'ph ph-building',
                    'title' => __('messages.collector_banks'),
                    'route' => 'backend.collector_bank.index',
                    'active' => ['app/collector_bank'],
                    'permission' => ['view_collector_bank'],
                    'order' => 0,
                ]);

            $this->mainRoute($menu, [
                'icon' => 'ph ph-user',
                'title' => __('messages.lbl_customer'),
                'route' => 'backend.customer.index',
                'active' => 'app/customer',
                'order' => 0,
                'permission' => ['view_customer'],
            ]);

            $this->mainRoute($menu, [
                'icon' => 'ph ph-users-four',
                'title' => __('messages.user'),
                'route' => 'backend.users.index',
                'active' => 'app/users',
                'order' => 0,
                'permission' => ['view_user'],
            ]);

            // Add Laboratory Management Menu Group
            $permissionsToCheck = ['view_lab','view_catelog','view_packages','view_prescription'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.laboratory_management'), 'order' => 0]);
            }

            // Labs
            $labs = $this->parentMenu($menu, [
                'icon' => 'ph ph-test-tube',
                'title' => __('messages.labs'),
                'nickname' => 'labs',
                'permission' => ['view_lab'],
                'order' => 0,
            ]);
            $this->childMain($labs, [
                'icon' => 'ph ph-test-tube',
                'title' => __('messages.labs'),
                'route' => 'backend.labs.index',
                'active' => ['app/labs'],
                'permission' => ['view_lab'],
                'order' => 0,
            ]);

            $this->childMain($labs, [
                'icon' => 'ph ph-clock',
                'title' => __('messages.lab_session'),
                'route' => 'backend.labsession.index',
                'active' => ['app/labsession'],
                'permission' => ['view_lab'],
                'order' => 0,
            ]);

            $testcase = $this->parentMenu($menu, [
                'icon' => 'ph ph-flask',
                'title' => __('messages.test_case'),
                'nickname' => 'Catalogs',
                'permission' => ['view_catelog'],
                'order' => 0,
            ]);

            $this->childMain($testcase, [
                'icon' => 'ph ph-flask',
                'title' => __('messages.test_category_list'),
                'route' => 'backend.categories.index',
                'active' => 'app/categories',
                'permission' => ['view_category'],
                'order' => 0,
            ]);

            $this->childMain($testcase, [
                'icon' => 'ph ph-microscope',
                'title' => __('messages.test_case_list'),
                'route' => 'backend.catlogmanagements.index',
                'active' => 'app/catlogmanagements',
                'permission' => ['view_catelog'],
                'order' => 0,
            ]);
            
            if(auth()->user()->hasRole('admin') || (auth()->user()->hasRole('vendor') && !auth()->user()->testPackageLimitReach())){
                $this->childMain($testcase, [
                    'icon' => 'ph ph-package',
                    'title' => __('messages.packages'),
                    'route' => 'backend.packagemanagements.index',
                    'active' => 'app/packagemanagements',
                    'permission' => ['view_packages'],
                    'order' => 0,
                ]);
            }
            
            // Prescriptions

            $prescriptions = $this->parentMenu($menu, [
                'icon' => 'ph ph-prescription',
                'title' => __('messages.prescriptions'),
                'nickname' => 'prescriptions',
                'permission' => ['view_prescription'],
                'order' => 0,
            ]);

            $this->childMain($prescriptions, [
                'icon' => 'ph ph-clipboard-text',
                'title' => __('messages.all_prescriptions'),
                'route' => 'backend.prescriptions.index',
                'active' => 'app/prescriptions',
                'permission' => ['view_prescription'],
                'order' => 0,
            ]);
            $this->childMain($prescriptions, [
                'icon' => 'ph ph-clock',
                'title' => __('messages.pending_prescriptions'),
                'route' => ['backend.prescriptions.pending','pending'],
                'active' => 'app/prescriptions/list/pending',
                'permission' => ['view_prescription'],
                'order' => 0,
            ]);
           
            if(multiVendor() == "1"){
            $permissionsToCheck = ['view_subscription', 'view_plans', 'view_planlimitation'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.subscription_plans'), 'order' => 0]);
            }

            
            $plan = $this->parentMenu($menu, [
               'icon' => 'ph ph-lock',
                'title' => __('messages.plans_price'),
                'nickname' => 'plans',
                'shortTitle' => 'p',
                'permission' => ['view_plans'],
                'order' => 0,
            ]);

            $this->childMain($plan, [
                'icon' => 'ph ph-lock',
                'title' => __('messages.plan'),
                'route' => 'backend.plans.index',
                'active' => ['app/plans'],
                'nickname' => 'plans',
                'shortTitle' => 'p',
                'permission' => ['view_plans'],
                'order' => 0,
            ]);

            $this->childMain($plan, [
                'icon' => 'ph ph-number-circle-one',
                'title' => __('messages.plan_limits'),
                'route' => 'backend.planlimitation.index',
                'active' => ['app/planlimitation'],
                'nickname' => 'planlimitation',
                'shortTitle' => 's',
                'order' => 0,
                'permission' => ['view_planlimitation'],
            ]);

            $this->childMain($plan, [
                'icon' => 'ph ph-hourglass',
                'title' => __('messages.plan_expire'),
                'nickname' => 'soon-to-expire',
                'route' => ['backend.users.soon-to-exipre', 'type' => 'soon-to-expire'],
                'shortTitle' => 'se',
                'active' => ['app/app/users/soon-to-expire'],
                'permission' => ['view_subscriptions'],
                'order' => 0,
            ]);

           
            $this->mainRoute($menu, [
                'icon' => 'ph ph-credit-card',
                'title' => __('messages.subscriptions'),
                'route' => 'backend.subscriptions.index',
                'active' => ['app/subscriptions'],
                'nickname' => 'subscription',
                'shortTitle' => 's',
                'order' => 0,
                'permission' => ['view_subscriptions'],
            ]);

        }

            $permissionsToCheck = ['view_reviews','view_coupon'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.rating_review'), 'order' => 0]);
            }

            $this->mainRoute($menu, [
                'icon' => 'ph ph-star',
                'title' => __('messages.reviews'),
                'route' => 'backend.reviews.index',
                'active' => ['app/reviews'],
                'nickname' => 'reviews',
                'shortTitle' => 'r',
                'permission' => ['view_reviews'],
                'order' => 0,
            ]);

           

            
            $permissionsToCheck = ['view_pages', 'view_faqs', 'view_documents'];

            // Check if user has at least one of the permissions
            $hasPermission = collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission));

            if ($hasPermission) {
                $this->staticMenu($menu, ['title' => __('messages.content_management'), 'order' => 0]);

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-note',
                    'title' => __('messages.pages'),
                    'route' => 'backend.pages.index',
                    'active' => ['app/pages'],
                    'nickname' => 'pages',
                    'shortTitle' => 'p',
                    'permission' => ['view_pages'],
                    'order' => 0,
                ]);

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-question',
                    'title' => __('messages.faqs'),
                    'route' => 'backend.faqs.index',
                    'active' => ['app/faqs'],
                    'nickname' => 'faqs',
                    'shortTitle' => 'f',
                    'permission' => ['view_faqs'],
                    'order' => 0,
                ]);

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-paperclip',
                    'title' => __('messages.documents'),
                    'route' => 'backend.documents.index',
                    'active' => ['app/documents'],
                    'permission' => ['view_documents'],
                    'order' => 0,
                ]);
            }


           

            $permissionsToCheck = [ 'view_vendor_payouts', 'view_collector_payouts', 'view_vendor_earnings', 'view_collector_earnings','view_payment_list','view_cash_payment_list','view_taxes','view_coupon'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.financial_management'), 'order' => 0]);
            }

            
       
            
            $this->mainRoute($menu, [
                'icon' => 'ph ph-receipt',
                'title' => __('messages.payment_list'),
                'route' => 'backend.payments.index',
                'active' => ['app/payments'],
                'permission' => ['view_payment_list'],
                'order' => 0,
            ]);
            $this->mainRoute($menu, [
                'icon' => 'ph ph-coins',
                'title' => __('messages.cash_payment_list'),
                'route' => ['backend.payments.cash_payment_list','cash'],
                'active' => ['app/payments/cash-payment-list/cash'],
                'permission' => ['view_cash_payment_list'],
                'order' => 0,
            ]);
            $payouts = $this->parentMenu($menu, [
                'icon' => 'ph ph-money',
                'title' => __('messages.payouts'),
                'nickname' => 'payouts',
                'permission' => ['view_vendor_payouts', 'view_collector_payouts'],
                'order' => 0,
            ]);
            if(multiVendor() == "1" && auth()->user()->user_type !== "vendor"){
            $this->childMain($payouts, [
                'icon' => 'ph ph-credit-card',
                'title' => __('messages.vendor_payouts'),
                'route' => 'backend.payouts.vendor_index',
                'active' => ['app/payouts/vendor-index'],
                'permission' => ['view_vendor_payouts'],
                'order' => 0,
            ]);
        }
            $this->childMain($payouts, [
                'icon' => 'ph ph-wallet',
                'title' => __('messages.collector_payouts'),
                'route' => 'backend.payouts.index',
                'active' => ['app/payouts'],
                'permission' => ['view_collector_payouts'],
                'order' => 0,
            ]);

            $earnings = $this->parentMenu($menu, [
                'icon' => 'ph ph-briefcase',
                'title' => __('messages.earnings'),
                'nickname' => 'earnings',
                'permission' => ['view_vendor_earnings', 'view_collector_earnings'],
                'order' => 0,
            ]);
            if(multiVendor() == "1"){
            $this->childMain($earnings, [
                'icon' => 'ph ph-database',
                'title' => __('messages.vendor_earnings'),
                'route' => 'backend.earnings.vendorEarning',
                'active' => ['app/vendor-earning'],
                'permission' => ['view_vendor_earnings'],
                'order' => 0,
            ]);
        }
            $this->childMain($earnings, [
                'icon' => 'ph ph-tip-jar',
                'title' => __('messages.collector_earnings'),
                'route' => 'backend.earnings.index',
                'active' => ['app/earnings'],
                'permission' => ['view_collector_earnings'],
                'order' => 0,
            ]);

            $this->mainRoute($menu, [
                'icon' => 'ph ph-calculator',
                'title' => __('messages.tax'),
                'route' => 'backend.taxes.index',
                'active' => ['app/taxes'],
                'permission' => ['view_taxes'],
                'order' => 0,
            ]);

            $this->mainRoute($menu, [
                'icon' => 'ph ph-gift',
                'title' => __('messages.coupons'),
                'route' => 'backend.coupons.index',
                'active' => ['app/coupons'],
                'nickname' => 'coupons',
                'shortTitle' => 'c',
                'order' => 0,
                'permission' => ['view_coupons'],
            ]);
           
            $permissionsToCheck = ['view_vendor_history','view_collector_history','view_reports'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.financial_management'), 'order' => 0]);
            }

            
            $history = $this->parentMenu($menu, [
                'icon' => 'ph ph-archive',
                'title' => __('messages.history'),
                'nickname' => 'history',
                'permission' => ['view_vendor_history', 'view_collector_history'],
                'order' => 0,
            ]);
            if(multiVendor() == "1"){
            $this->childMain($history, [
                'icon' => 'ph ph-clock-counter-clockwise',
                'title' => __('messages.vendor_history'),
                'route' => 'backend.activity-log-vendor.index',
                'active' => ['app/activity-log/vendor'],
                'permission' => ['view_vendor_history'],
                'order' => 0,
            ]);
        }
            $this->childMain($history, [
                'icon' => 'ph ph-scroll',
                'title' => __('messages.collector_history'),
                'route' => 'backend.activity-log-collector.index',
                'active' => ['app/activity-log/collector'],
                'permission' => ['view_collector_history'],
                'order' => 0,
            ]);
            
            $report = $this->parentMenu($menu, [
                'icon' => 'ph ph-chart-line',
                'title' => __('messages.reports'),
                'nickname' => 'report',
                'permission' => ['view_reports'],
                'order' => 0,
            ]);

            if(auth()->user()->user_type !== "vendor"){
                $this->childMain($report, [
                    'icon' => 'ph ph-chart-line-up',
                    'title' => __('messages.earning_report'),
                    'route' => 'backend.reports.index',
                    'active' => ['app/reports'],
                    'permission' => ['view_reports'],
                    'order' => 0,
                ]);
            }
                if(multiVendor() == "1" && auth()->user()->hasRole(['admin','demo_admin'])){
                $this->childMain($report, [
                    'icon' => 'ph ph-currency-circle-dollar',
                    'title' => __('messages.vendor_subscription'),
                    'route' => 'backend.reports.vendor_subscription',
                    'active' => ['app/reports/vendor-subscription'],
                    'permission' => ['view_reports'],
                    'order' => 0,
                ]);
            }
               
                $this->childMain($report, [
                    'icon' => 'ph ph-sort-descending',
                    'title' => __('messages.top_test_case'),
                    'route' => 'backend.reports.top_testcase_booked',
                    'active' => ['app/reports/top-testcase-booked'],
                    'permission' => ['view_reports'],
                    'order' => 0,
                ]);

           

           
            $permissionsToCheck = ['view_helpdesks'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.helpdesks_management'), 'order' => 0]);
            }
            $this->mainRoute($menu, [
                'icon' => 'ph ph-chat',
                'title' => __('messages.help_desks'),
                'route' => 'backend.helpdesks.index',
                'active' => ['app/helpdesks'],
                'permission' => ['view_helpdesks'],
                'order' => 0,
            ]);



           
            $permissionsToCheck = ['view_setting'];

            if (collect($permissionsToCheck)->contains(fn($permission) => auth()->user()->can($permission))) {
                $this->staticMenu($menu, ['title' => __('messages.system_setting'), 'order' => 0]);
            }
            $this->mainRoute($menu, [
                'icon' => 'ph ph-layout',
                'title' => __('messages.app_banner'),
                'route' => 'backend.banners.index',
                'active' => ['app/banners'],
                'permission' =>['view_banner'],
                'order' => 0,
            ]);
            $this->mainRoute($menu, [
                'icon' => 'ph ph-gear-six',
                'title' => __('messages.settings'),
                'route' => 'backend.settings.general',
                'active' => 'app/setting/general-setting',
                 'permission' => ['view_setting'],
                'order' => 0,
            ]);

            $notification = $this->parentMenu($menu, [
                'icon' => 'ph ph-bell',
                'title' => __('messages.notification'),
                'nickname' => 'notifications',
                'permission' => ['view_notification'],
                'order' => 0,
            ]);

            $this->childMain($notification, [
                'icon' => 'ph ph-list-bullets',
                'title' => __('messages.notification_list'),
                'route' => 'backend.notifications.index',
                'shortTitle' => 'Li',
                'active' => 'app/notifications',
                'permission' => ['view_notification'],
                'order' => 0,
            ]);
            $this->childMain($notification, [
                'icon' => 'ph ph-layout',
                'title' => __('messages.notification.template'),
                'route' => 'backend.notification-templates.index',
                'shortTitle' => 'TE',
                'active' => 'app/notification-templates',
                'permission' => ['view_notification_template'],
                'order' => 0,
            ]);


            if (auth()->user()->hasRole('admin')) {
                $this->staticMenu($menu, ['title' => __('messages.account_settings'), 'order' => 0]);

                $this->mainRoute($menu, [
                    'icon' => 'ph ph-faders',
                    'title' => __('messages.access_control'),
                    'route' => 'backend.permission-role.list',
                    'active' => ['app/permission-role'],
                    'order' => 10,
                ]);
            }

            

            

            



      
            $menu->filter(function ($item) {
                if ($item->data('permission')) {
                    if (auth()->check()) {
                        if (\Auth::getDefaultDriver() == 'admin') {
                            return true;
                        }
                        if (auth()->user()->hasAnyPermission($item->data('permission'), \Auth::getDefaultDriver())) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return true;
                }
            });
       
            $menu->filter(function ($item) {
                if ($item->activematches) {
                    $activematches = (is_string($item->activematches)) ? [$item->activematches] : $item->activematches;
                    foreach ($activematches as $pattern) {
                        if (request()->is($pattern)) {
                            $item->active();
                            $item->link->active();
                            if ($item->hasParent()) {
                                $item->parent()->active();
                            }
                        }
                    }
                }

                return true;
            });

           
        })->sortBy('order');
    }
}

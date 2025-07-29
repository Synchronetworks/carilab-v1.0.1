<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Trait\ActivityLogger;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\Subscription;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Storage;
use Modules\Lab\Models\Lab;
use Modules\Collector\Models\Collector;
use Modules\Commision\Models\CommissionEarning;
use Modules\Prescription\Models\Prescription;
use Illuminate\Support\Facades\Artisan;
use Modules\Payout\Models\Payout;

class BackendController extends Controller
{
    use ActivityLogger;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Fetch all regular users

        $action = $request->action ?? 'reset';
        if (isset($request->date_range) && $action !== 'reset') {

            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) { 
                $startDate = $dates[0] ?? date('Y-m-d');
                $endDate = $dates[1] ?? date('Y-m-d');
            } elseif (count($dates) == 1) { 
                $startDate = $dates[0] ?? date('Y-m-d');
                $endDate = $startDate; 
            } else { 
                $startDate = Carbon::now()->startOfMonth()->toDateString();
                $endDate = Carbon::now()->toDateString();
            }
        } else {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->toDateString();
        }

        $date_range = $startDate . ' to ' . $endDate;
        $allUsers = User::where('user_type', 'user')->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->get();


        $activeVendors = User::where('user_type', 'vendor')->where('status', 1)->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
       
        
        $activeCollectors = User::myCollector()
            ->where('user_type', 'collector')
            ->where('status', 1)
            ->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $activeCollectors = $activeCollectors->count();

        $totalLabs = Lab::myLabs()
            ->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        $activeCustomers = User::where('user_type', 'user')->where('status', 1)->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
        $totalAppointments = Appointment::MyAppointment()
                            ->where('created_at', '>=', $startDate)
                            ->whereDate('created_at', '<=', $endDate)->count();
        $totalRevenue = 0;
        $adminEarnings = 0; 


        $testAppointments = Appointment::MyAppointment()->where('test_type', 'test_case')->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
        $packageAppointments = Appointment::MyAppointment()->where('test_type', 'test_package')->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();

        $appointmentsData = [
            'testAppointments' => $testAppointments,
            'packageAppointments' => $packageAppointments,
        ];

        $query = User::role('user');
        $currentDate = Carbon::now();
        $expiryThreshold = $currentDate->copy()->addDays(7);
        $subscriptions = Subscription::with('user')
            ->where('status', 'active')
            ->whereDate('end_date', '<=', $expiryThreshold)
            ->get();
        $userIds = $subscriptions->pluck('user_id');
        $soonToExpireVendors = User::with('subscriptionPackage')->where('user_type', 'vendor')->whereIn('id', $userIds)->get();

        $newVendors = collect();
        $newVendors = User::where('user_type', 'vendor')->where('status', 1)->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->orderBY('id', 'desc')->take(5)->get();

        $pendingCollectors = collect();
        $pendingCollectors = User::myCollector()
            ->where('user_type', 'collector')
            ->where('status', 0)
            ->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->take(5)->get();
        
        $totalTransactions = SubscriptionTransactions::count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $newUsersCount = User::where('user_type', 'user')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->count();
        $totalusers = $allUsers->count();
        $activeusers = $allUsers->where('status', 1)->count();
        $totalSubscribers = $allUsers->where('is_subscribe', 1)->count();
        $currentDate = Carbon::now();
        $expiryThreshold = $currentDate->copy()->addDays(7);
        $subscriptions = Subscription::with('user')
            ->where('status', 'active')
            ->whereDate('end_date', '<=', $expiryThreshold)
            ->get();
        $userIds = $subscriptions->pluck('user_id');
        $totalsoontoexpire = $allUsers->whereIn('id', $userIds)->where('status', 1)->count();


        $transactions = SubscriptionTransactions::orderBy('created_at', 'desc')
            ->where('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->take(4)
            ->get();




        $user = auth()->user();
        $subscriptionData = Subscription::with('user', 'subscription_transaction', 'plan')->orderBy('updated_at', 'desc')->take(6)->get();
        
        $totalpendingpayout = (new CommissionEarning)->rolewiseCommission(auth()->user(),'unpaid',$startDate,$endDate);
        
        $vendorActiveCustomers = Appointment::where('vendor_id', $user->id)
                                ->where('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate)
                                ->distinct('customer_id')
                                ->count();

        $pendingPrescriptions = Prescription::MyPrescription()->where('prescription_status',0)->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        $totalUsageFormatted = 0;
        Artisan::call('optimize:clear');

        // Calculate commission revenue
        $commission_revenue = $user->user_type == 'vendor' ? Payout::where('user_id', $user->id)
        ->where('paid_date','>=', $startDate)
        ->whereDate('paid_date','<=', $endDate)
        ->where('user_type', 'vendor')
        ->sum('amount') ?? 0 : (new CommissionEarning)->rolewiseCommission($user, 'paid',$startDate,$endDate);
        
        // Calculate subscription revenue (only for admin)
        $subscription_revenue = 0;
        if ($user->user_type !== 'vendor') {
            $subscription_revenue = SubscriptionTransactions::where('payment_status', 'paid')
                ->when(isset($startDate) && isset($endDate), function($query) use ($startDate, $endDate) {
                    return $query->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                })
                ->sum('amount');   
        }

        // Calculate total revenue (subscription + commission)
        $total_revenue = $commission_revenue + $subscription_revenue;
        $activeSubscriptions = Subscription::where('user_id', auth()->id())->where('status', 'active')->where('end_date', '>', now())->orderBy('id','desc')->first();
        return view('backend.dashboard.index', compact(
            'activeVendors',
            'activeCollectors',
            'totalLabs',
            'date_range',
            'activeCustomers',
            'totalAppointments',
            'total_revenue',
            'commission_revenue',
            'subscription_revenue',
            'appointmentsData',
            'soonToExpireVendors',
            'newVendors',
            'pendingCollectors',
            'pendingPrescriptions',
            'totalpendingpayout',
            'vendorActiveCustomers',
            'activeSubscriptions'
        ));
    }

    private function getTotalStorageUsage($disk)
    {
        $totalSize = 0;
        $files = Storage::disk($disk)->allFiles();

        foreach ($files as $file) {
            $fileSize = Storage::disk($disk)->size($file);

            $totalSize += $fileSize;
        }
        return $totalSize;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    


    private function getAppointmentTrendsData($timeRange)
    {

        
        if (!class_exists('Modules\Appointment\Models\Appointment')) {
            
            return response()->json([
                'values' => [10, 20, 15, 30, 40], 
                'labels' => ['2021', '2022', '2023', '2024', '2025'], 
            ]);
        }
        
        switch ($timeRange['time_range']) {
            case 'Year':
                $appointments = Appointment::selectRaw('COUNT(*) as total, YEAR(created_at) as year')
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get();
                break;

            case 'Month':
                $appointments = Appointment::selectRaw('COUNT(*) as total, MONTH(created_at) as month')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                break;
            case 'Week':
                $appointments = Appointment::selectRaw('COUNT(*) as total, WEEK(created_at) as week')
                    ->groupBy('week')
                    ->orderBy('week')
                    ->get();
                break;
            default:
                $appointments = collect();
        }
        // Return the dynamic data from the database
        return response()->json([
            'values' => $appointments->pluck('total')->toArray(),
            'labels' => match ($timeRange['time_range']) {
                'Year' => $appointments->pluck('year')->toArray(),
                'Month' => $appointments->pluck('month')->map(fn($month) => date('F', mktime(0, 0, 0, $month, 10)))->toArray(),
                'Week' => $appointments->pluck('week')->map(fn($week) => "Week {$week}")->toArray(),
                default => [],
            },
        ]);
    }


    private function getAppointmentStatusDistribution($params)
    {
        
        if (!class_exists('Modules\Appointment\Models\Appointment')) {
            
            return response()->json([
                'status' => ['Scheduled', 'In Progress', 'Completed'],
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'values' => [
                    [30, 40, 35, 50, 60, 55, 70],  // Scheduled
                    [20, 25, 30, 35, 40, 45, 50],  // In Progress
                    [10, 15, 20, 25, 30, 35, 40],  // Completed
                ],
            ]);
        }

        
        $filter = $params['filter'] ?? 'monthly';

        
        $query = Appointment::selectRaw('status, COUNT(*) as total, DATE(created_at) as date')
            ->groupBy('status', 'date');

        
        switch ($filter) {
            case 'daily':
                $query->whereDate('created_at', '>=', now()->subDays(7));
                break;
            case 'yearly':
                $query->whereYear('created_at', now()->year);
                break;
            default: // Monthly
                $query->whereMonth('created_at', now()->month);
        }

        // Get the results
        $statusCounts = $query->get();

        // Prepare the data for the chart
        $statusLabels = ['Accepted', 'In Progress', 'Completed'];
        $statusLabels1 = ['accept', 'in_progress', 'completed'];
        // Group the results by date and prepare the values
        $groupedData = $statusCounts->groupBy('date');

        $dates = $groupedData->keys()->toArray();

        // Prepare values for each status
        $values = [];
        foreach ($statusLabels1 as $status) {
            $statusValues = [];
            foreach ($dates as $date) {
                $statusValues[] = $groupedData[$date]->where('status', $status)->sum('total');
            }
            $values[] = $statusValues;
        }

        // Return the final JSON response
        return response()->json([
            'status' => $statusLabels,
            'labels' => $dates,
            'values' => $values,
        ]);
    }


    private function getMonthlyRevenueTrend($params)
    {
        $year = date('Y');
        $user = auth()->user(); // Get the authenticated user

        // Fetch subscription revenue grouped by month (Only if user is NOT a vendor)
        $subscriptions = [];
        if ($user->user_type !== 'vendor') {
            $subscriptions = SubscriptionTransactions::selectRaw('SUM(amount) as total, MONTH(updated_at) as month')
                ->whereYear('updated_at', $year)
                ->where('payment_status', 'paid')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
        }

        // Fetch commission revenue grouped by month
        $commissions = $user->user_type == 'vendor' ? Payout::selectRaw('SUM(amount) as total, MONTH(updated_at) as month')
            ->whereYear('updated_at', $year)
            ->where('user_id', $user->id)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray() : CommissionEarning::selectRaw('SUM(commission_amount) as total, MONTH(updated_at) as month')
            ->whereYear('updated_at', $year)
            ->where('employee_id', $user->id)
            ->where('commission_status', 'paid')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Month names array
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // Prepare chart data (Ensure all 12 months are included)
        $months = [];
        $subscriptionsData = array_fill(1, 12, 0); // Default values for 12 months
        $commissionsData = array_fill(1, 12, 0);

        for ($month = 1; $month <= 12; $month++) {
            $months[] = $monthNames[$month]; // Get month name directly from array
            $subscriptionsData[$month] = $subscriptions[$month] ?? 0;
            $commissionsData[$month] = $commissions[$month] ?? 0;
        }

        return response()->json([
            'months' => $months,
            'subscriptions' => array_values($subscriptionsData), // Will be all zeros if vendor
            'commissions' => array_values($commissionsData),
        ]);
    }



    public function getChartData(Request $request)
    {
        // Get the graph name and parameters from the request
        $graphName = $request->get('graph_name');
        $params = $request->all();

        // Based on the graph name, call the respective data-fetching method
        switch ($graphName) {
            case 'appointmentTrends':
                return $this->getAppointmentTrendsData($params);
            case 'appointmentStatusDistribution':
                return $this->getAppointmentStatusDistribution($params);
            case 'monthlyRevenueTrend':
                return $this->getMonthlyRevenueTrend($params);
            // Add more cases for other graphs as needed
            default:
                return response()->json(['error' => __('messages.invalid_graph_name')]);
        }
    }

    public function approve($type, $id)
    {
        // Find the record based on the type and ID
        if ($type === 'vendor') {
            $record = User::findOrFail($id);
        } elseif ($type === 'collector') {
            $record = User::findOrFail($id);
        } else {
            return response()->json(['message' => __('messages.invalid_type')], 400);
        }

        // Before update, check the status


        // Update the status field using update()
        $record->update(['status' => 1,'is_available' => 1]); // Assuming '1' means approved

        // After update, check the status
        // This should now be updated

        // Log the activity (Ensure your log method is working)
        $this->logActivity('approve', $record, 'record_approved');

        // Return a response with a success message
        return response()->json(['message' => __('messages.status_updated')]);
    }

    public function getRevenuechartData(Request $request, $type)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $vendor_id = $request->vendor_id ?? null;
        $lab_id = $request->lab_id ?? null;

      

        // Base Query Builder Functions
        $baseVendorQuery = function ($query) use ($lab_id, $vendor_id) {
            $query->where('lab_id', $lab_id)->where('vendor_id', $vendor_id);
        };

        $baseLabQuery = function ($query) use ($lab_id) {
            $query->where('lab_id', $lab_id)
                  ->whereHas('transactions', function($q) {
                      $q->where('payment_status', 'paid');
                  });
        };

        $chartData = [];
        $category = [];

        switch ($type) {
            case 'Year':
                if ($vendor_id) {
                    // Vendor revenue calculation
                    $monthlyTotals = CommissionEarning::whereHas('getAppointment', $baseVendorQuery)
                        ->selectRaw('MONTH(updated_at) as month, SUM(commission_amount) as total_amount')
                        ->where('commission_status', 'paid')
                        ->where('employee_id', $vendor_id)
                        ->whereYear('updated_at', $currentYear)
                        ->groupBy('month')
                        ->orderBy('month')
                        ->pluck('total_amount', 'month')
                        ->toArray();
                } else {
                    // Lab revenue calculation
                    $monthlyTotals = Appointment::where(function ($query) use ($baseLabQuery) {
                            $baseLabQuery($query);
                        })
                        ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total_amount')
                        ->whereYear('created_at', $currentYear)
                        ->groupBy('month')
                        ->orderBy('month')
                        ->pluck('total_amount', 'month')
                        ->toArray();

                 
                        
                }

                for ($month = 1; $month <= 12; $month++) {
                    $chartData[] = isset($monthlyTotals[$month]) ? floatval($monthlyTotals[$month]) : 0;
                }
                $category = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                break;

            case 'Month':
                if ($vendor_id) {
                    // Vendor monthly data
                    $dailyTotals = CommissionEarning::whereHas('getAppointment', $baseVendorQuery)
                        ->selectRaw('DAY(updated_at) as day, SUM(commission_amount) as total_amount')
                        ->where('commission_status', 'paid')
                        ->where('employee_id', $vendor_id)
                        ->whereMonth('updated_at', $currentMonth)
                        ->whereYear('updated_at', $currentYear)
                        ->groupBy('day')
                        ->orderBy('day')
                        ->pluck('total_amount', 'day')
                        ->toArray();
                } else {
                    // Lab monthly data
                    $dailyTotals = Appointment::where(function ($query) use ($baseLabQuery) {
                            $baseLabQuery($query);
                        })
                        ->selectRaw('DAY(created_at) as day, SUM(total_amount) as total_amount')
                        ->whereMonth('created_at', $currentMonth)
                        ->whereYear('created_at', $currentYear)
                        ->groupBy('day')
                        ->orderBy('day')
                        ->pluck('total_amount', 'day')
                        ->toArray();
                }

                $daysInMonth = Carbon::now()->daysInMonth;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $chartData[] = isset($dailyTotals[$day]) ? floatval($dailyTotals[$day]) : 0;
                    $category[] = (string)$day;
                }
                break;

            case 'Week':
                $startOfWeek = Carbon::now()->startOfWeek();
                
                for ($day = 0; $day < 7; $day++) {
                    $date = $startOfWeek->copy()->addDays($day);
                    
                    if ($vendor_id) {
                        // Vendor weekly data
                        $amount = CommissionEarning::whereHas('getAppointment', $baseVendorQuery)
                            ->where('commission_status', 'paid')
                            ->where('employee_id', $vendor_id)
                            ->whereDate('updated_at', $date)
                            ->sum('commission_amount');
                    } else {
                        // Lab weekly data
                        $amount = Appointment::where(function ($query) use ($baseLabQuery) {
                                $baseLabQuery($query);
                            })
                            ->whereDate('created_at', $date)
                            ->sum('total_amount');
                    }

                    $chartData[] = floatval($amount);
                    $category[] = $date->format('D');
                }
                break;
        }

        return response()->json([
            'data' => [
                'chartData' => $chartData,
                'category' => $category
            ],
            'status' => true
        ]);
    }
}

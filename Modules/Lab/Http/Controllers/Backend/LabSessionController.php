<?php

namespace Modules\Lab\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Lab\Models\Lab;
use Yajra\DataTables\DataTables;
use Modules\Lab\Models\LabSession;
use App\Trait\ModuleTrait;
use Modules\Lab\Trait\HasTaxList;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;

class LabSessionController extends Controller
{ 
    protected string $exportClass = '\App\Exports\LabSessionExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.lab_session', // module title
            'labsession', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $module_action = __('messages.list');
        $module_title = trans('messages.lab_session');
        $export_import = true;
        $export_columns = [
            ['value' => 'lab', 'text' => __('messages.lbl_lab')],
            ['value' => 'day', 'text' => __('messages.lbl_day')],
        ];
        $export_url = route('backend.labsession.export');
        return view('lab::backend.labsession.index', compact('module_action','module_title', 'export_import', 'export_columns', 'filter', 'export_url'));
    }
    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Lab::myLabs()
                // ->query()
                ->withTrashed();
        $lab_id = $request->query('lab_id');
        if ($lab_id && $lab_id !== null) {
            $query->where('id', $lab_id); // Changed 'lab_id' to 'id'
        }
        $filter = $request->filter;
        if (!empty($filter['lab_id'])) {
            $query->where('id', $filter['lab_id']); // Changed 'lab_id' to 'id'
        }
        return $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" name="select_all_table"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="labsession" onclick="dataTableRowCheck('.$data->id.',this)">';
            })  
            ->addColumn('lab_id', function ($data) {
                return view('lab::backend.lab_details', compact('data'));
            })
            ->filterColumn('lab_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'like', '%' . $keyword . '%')
                                 ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('lab_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(name) $order");
            })
            ->addColumn('day', function ($data) {
                $allDays = LabSession::where('lab_id', $data->id)
                ->where('is_holiday', '===','0')
                ->pluck('day')
                ->toArray();

                return implode(',', $allDays);
            })
            ->filterColumn('day', function ($query, $keyword) {
                // Split the keyword into an array of days
                $days = array_map('trim', explode(',', $keyword));
            
                // Filter the query based on days
                $query->whereHas('labSessions', function ($subQuery) use ($days) {
                    $subQuery->where('is_holiday', 0) // Ensure we only consider non-holidays
                        ->where(function ($nestedQuery) use ($days) {
                            foreach ($days as $day) {
                                $nestedQuery->orWhere('day', 'like', '%' . $day . '%');
                            }
                        });
                });
            })
            ->addColumn('action', function ($data) {
                return view('lab::backend.labsession.action', compact('data'));
            })
            ->rawColumns(['name', 'action', 'status', 'check', 'day'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $labs = Lab::myLabs()->where('status', 1)
            ->doesntHave('labSessions')
            ->get();

        return view('lab::backend.labsession.create', compact('labs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $lab_id = $data['lab_id'];
        $weekdays = $data['days'];

        foreach ($weekdays as $day => $value) {
            $sessionData = [
                'lab_id' => $lab_id,
                'day' => $day,
                'start_time' => $value['start_time'] ?? null,
                'end_time' => $value['end_time'] ?? null,
                'is_holiday' => isset($value['is_holiday']) ? 1 : 0,
            ];

            // Format breaks data if exists
            if (isset($value['breaks']) && is_array($value['breaks'])) {
                $formattedBreaks = [];
                for ($i = 0; $i < count($value['breaks']); $i += 2) {
                    if (isset($value['breaks'][$i]['start_break']) && 
                        isset($value['breaks'][$i + 1]['end_break'])) {
                        $formattedBreaks[] = [
                            'start_break' => $value['breaks'][$i]['start_break'],
                            'end_break' => $value['breaks'][$i + 1]['end_break']
                        ];
                    }
                }
                $sessionData['breaks'] = !empty($formattedBreaks) ? json_encode($formattedBreaks) : null;
            } else {
                $sessionData['breaks'] = null;
            }

            // Update or create the lab session
            LabSession::updateOrCreate(
                [
                    'lab_id' => $lab_id,
                    'day' => $day
                ],
                $sessionData
            );
        }
        $message=__('messages.update_lab_session');
        return redirect()->route('backend.labsession.index')
            ->with('success', $message);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('lab::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $labs = Lab::all();
        $labSessions = LabSession::with('lab')->myLabsession()->where('lab_id', $id)->get();
        if($labSessions->isEmpty()){
            return redirect()->route('backend.labsession.index')->with('error', __('messages.lab_session_not_found'));
        }
        // Format the data for the view
        $data = new \stdClass();
        $data->lab_id = $id;
        $data->days = [];
        
        foreach ($labSessions as $session) {
            $data->days[$session->day] = [
                'id' => $session->id,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
                'is_holiday' => $session->is_holiday,
                'breaks' => is_string($session->breaks) ? json_decode($session->breaks, true) : $session->breaks
            ];
        }

        return view('lab::backend.labsession.create', compact('labs', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
    public function availableSlot(Request $request)
    {
        $availableSlot = [];

        if ($request->has('appointment_date') && $request->has('lab_id')) {
            $timezone = new \DateTimeZone(setting('default_time_zone') ?? 'UTC');
            $lab = Lab::find($request->lab_id);
           
            // Default time slot duration (in minutes)
            $time_slot_duration = $lab->time_slot ?? 30; // You can adjust this or fetch from lab settings

            $currentDate = Carbon::today($timezone);
            $carbonDate = Carbon::parse($request->appointment_date, $timezone);
            
            $dayOfWeek = strtolower($carbonDate->dayName);

            // Get lab session for the specific day
            $labSession = LabSession::where('lab_id', $request->lab_id)
                ->where('day', $dayOfWeek)
                ->first();
                
            if ($labSession && !$labSession->is_holiday) {
                $startTime = Carbon::parse($labSession->start_time, $timezone);
                $endTime = Carbon::parse($labSession->end_time, $timezone);
               
                $breaks = is_string($labSession->breaks) ? json_decode($labSession->breaks, true) ?? [] : ($labSession->breaks ?? []);

                $timeSlots = [];

                $current = $startTime->copy();
                while ($current < $endTime) {
                    $inBreak = false;
                    foreach ($breaks as $break) {
                        $breakStartTime = Carbon::parse($break['start_break'], $timezone);
                        $breakEndTime = Carbon::parse($break['end_break'], $timezone);
                        if ($current >= $breakStartTime && $current < $breakEndTime) {
                            $inBreak = true;
                            break;
                        }
                    }

                    if (!$inBreak) {
                        $timeSlots[] = $current->format('H:i');
                    }

                    $current->addMinutes($time_slot_duration);
                }

                $availableSlot = $timeSlots;

                // Handle current day slots
                if ($carbonDate->isSameDay($currentDate)) {
                    $todayTimeSlots = [];
                    $currentDateTime = Carbon::now($timezone);
                    foreach ($timeSlots as $slot) {
                        $slotTime = Carbon::parse($slot, $timezone);
                        if ($slotTime->greaterThan($currentDateTime)) {
                            $todayTimeSlots[] = $slotTime->format('H:i');
                        }
                    }
                    $availableSlot = $todayTimeSlots;
                }

                // Remove booked slots
                $appointments = Appointment::where('lab_id', $request->lab_id)
                    ->where('appointment_date', $request->appointment_date)
                    ->where('status', '!=', 'cancelled')
                    ->get();

                $bookedSlots = [];
                foreach ($appointments as $appointment) {
                    $bookedSlots[] = Carbon::parse($appointment->appointment_time)->format('H:i');
                }

                $availableSlot = array_values(array_diff($availableSlot, $bookedSlots));
            }
        }
        
        if ($request->is('api/*')) {
            return response()->json([
                'message' => __('messages.available_slots_retrive'),
                'data' => $availableSlot,
                'status' => true
            ], 200);
        }

        return response()->json([
            'data' => $availableSlot,
            'status' => true
        ]);
    }
}

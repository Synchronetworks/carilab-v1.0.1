<?php

namespace Modules\Prescription\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Prescription\Models\Prescription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Prescription\Http\Requests\PrescriptionRequest;
use App\Trait\ModuleTrait;
use App\Models\User;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Lab\Models\Lab;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Prescription\Models\PrescriptionPackageMapping;
use Modules\Prescription\Models\PrescriptionLabMapping;
use App\Jobs\BulkNotification;
use Modules\Prescription\Trait\PrescriptionTrait;
use App\Trait\NotificationTrait;
use \App\Models\Setting;
class PrescriptionsController extends Controller
{
    use ActivityLogger;
    use PrescriptionTrait;
    use NotificationTrait;
    protected string $exportClass = '\App\Exports\PrescriptionExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            __('messages.prescriptions'), // module title
            'prescriptions', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $type = 'prescription';
        $module_action = __('messages.list');
        $module_title= __('messages.all_prescriptions');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'customer',
                'text' => __('messages.name'),
            ],
            [
                'value' => 'uploaded_at',
                'text' => __('messages.lbl_uploaded_at'),
            ],
            [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $export_url = route('backend.prescriptions.export');

        return view('prescription::backend.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','module_title','type'));
    }
    public function pending(Request $request,$approval_status)
    {
        $filter = [
            'status' => $request->status,
        ];
        $type = 'prescription';
        $module_title= __('messages.pending_prescriptions');
        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'customer',
                'text' => __('messages.name'),
            ],
            [
                'value' => 'uploaded_at',
                'text' => __('messages.lbl_uploaded_at'),
            ],
            [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
       
        $export_url = route('backend.prescriptions.export',['prescriptions_status' => $approval_status]);

        return view('prescription::backend.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','approval_status','module_title','type'));
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'Prescription'; // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Prescription::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Prescription::MyPrescription()->with('labMappings');
        $query = $query->where('prescription_status', $request->approval_status == 'pending' ? 0 : 1);
        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
          
        ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="prescription" onclick="dataTableRowCheck('.$data->id.',this)">';
        })
        ->addColumn('user', function($data) {
            $data = User::find($data->user_id);
            return view('user::backend.users.user_details', compact('data'));
        })
        ->filterColumn('user', function ($query, $keyword) {
            if (!empty($keyword)) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('first_name', 'like', "%{$keyword}%")
                             ->orWhere('last_name', 'like', "%{$keyword}%")
                             ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"])
                             ->orWhere('email', 'like', "%{$keyword}%");
                });
            }
        })
           
        ->addColumn('uploaded_at', function($data) {
            return Setting::formatDate($data->uploaded_at);
        })
        ->addColumn('lab', function($data) {
            $labIds = $data->labMappings->pluck('lab_id')->unique();
           
            $data = Lab::whereIn('id', $labIds)->first();
            return view('lab::backend.lab_details', compact('data'));
        })
        ->filterColumn('lab', function ($query, $keyword) {
            if (!empty($keyword)) {
                $query->whereHas('labMappings.lab', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                             ->orWhere('email', 'like', "%{$keyword}%");
                });
            }
        })
        ->orderColumn('lab', function ($query, $order) {
            $query->join('labs', 'labs.id', '=', 'lab_mappings.lab_id') 
                  ->orderBy('labs.name', $order);
        })
          ->addColumn('action', function ($data) {
              return view('prescription::backend.action', compact('data'));
          })
          ->editColumn('status', function ($data) {
              return $data->getStatusLabelAttribute();
          })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check','user','uploaded_at','lab','catlog','package'])
          ->orderColumns(['id'], '-:column $1')
          ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

      public function create()
    {
        $module_title = trans('messages.new_prescription');
      return view('prescription::backend.create',compact('module_title'));
    }

    public function store(PrescriptionRequest $request)
    {
        $data = $request->except('lab_id'); // Ensure lab_id is not inserted in prescriptions table
        $data['user_id'] = $request->user_id ?? auth()->id();
        $data['uploaded_at'] = $request->uploaded_at ?? now();

        $prescription = Prescription::create($data);

        // Store lab mapping separately
        if (!empty($request->lab_id)) {
            PrescriptionLabMapping::create([
                'prescription_id' => $prescription->id,
                'lab_id' => $request->lab_id,
            ]);
        }

        $this->logActivity('create', $prescription, 'prescription_create');

        if ($request->has('prescription_upload')) {
            storeMediaFile($prescription, $request->file('prescription_upload'), 'prescription_upload');
        }

        $message = __('messages.prescription_add');

        if ($request->is('api/*')) {
            return response()->json([
                'message' => $message,
                'prescription_id' => $prescription->id
            ], 200);
        }

      
        $message=__('messages.record_add');
        return redirect()->route('backend.prescriptions.index')->with('success',$message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Prescription::findOrFail($id);
        $module_title = trans('messages.edit_prescription');
    return view('prescription::backend.edit', compact('data','moduel_title'));

    }

    public function show($id)
    {
        $data = Prescription::MyPrescription()->with('labMappings')->where('id',$id)->first();
        if($data == null){
            return redirect()->route('backend.prescriptions.index')->with('error', __('record_not_found'));
        }
        // Get the associated lab_id from labMappings (if exists)
        $labMapping = $data->labMappings->first(); // Assuming one lab per prescription
        $data['lab_id'] = $labMapping ? $labMapping->lab_id : null;

        // Fetch only the assigned lab if exists, else fetch all active labs
        $labs = $data['lab_id']
            ? Lab::where('id', $data['lab_id'])->get()
            : Lab::where('status', 1)->get();
        // Fetch the prescriptionLabs along with lab and testMapping relationships
        $prescriptionLabs = $data->labMappings()->with(['lab', 'testMapping'])->get();

        // Access the test attribute for each labMapping
        $prescriptionLabs->each(function ($labMapping) {
            if ($labMapping->testMapping) {
                $labMapping->test_mapping = $labMapping->testMapping->getTestAttribute();
            }
        });

        if ($prescriptionLabs->isEmpty()) {
            $prescriptionLabs = null; // or an empty array
        }
        return view('prescription::backend.show', compact('data','labs','prescriptionLabs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(PrescriptionRequest $request, Prescription $prescription)
    {
        $requestData = $request->all();
        $prescription->update($requestData);
        $this->logActivity('update',$prescription,'prescription_update');
        $message=__('messages.update_form');
        return redirect()->route('backend.prescriptions.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Prescription::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'prescription_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    
    }

    public function restore($id)
    {
        $data = Prescription::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'prescription_restore');
        $message = __('messages.restore_form');
        return response()->json(['message' =>  $message]);}

    public function forceDelete($id)
    {
        $data = Prescription::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'prescription_force_delete');
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' =>  $message]);
    }

    

    public function viewDocument($id)
    {
        $data = Prescription::findOrFail($id);
        $filePath = $data->getFirstMediaPath('prescription_upload'); // Get file path directly
    
        if (file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            return response(file_get_contents($filePath), 200)
            ->header('Content-Type', 'application/pdf,image/jpeg,image/jpg,image/png') // Make sure Content-Type is set to application/pdf
            ->header('Content-Disposition', 'inline; filename="' . basename($filePath) . '"'); // Use inline, not attachment
        }
    
        return abort(404, __('messages.file_not_found'));
    }

    public function downloadDocument($id)
    {
        try {
            $prescription = Prescription::findOrFail($id);
            $documentPath = $prescription->getFirstMediaPath('prescription_upload'); // Get actual file path

            if (!$documentPath || !file_exists($documentPath)) {
                return back()->with('error', __('messages.file_not_found'));
            }

            // Get file name
            $fileName = basename($documentPath);

            // Force download using Storage facade
            return response()->download($documentPath, $fileName);           
        } catch (\Exception $e) {
            return back()->with('error',__('messages.error_downloading_document') . $e->getMessage());
        }
    }
  

    public function addSelection(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescriptionTestMapping = [];

        // Collect and filter test cases (catalogs & packages)
        $catalogs = CatlogManagement::whereIn('id', $request->catalog_ids ?? [])->get()->keyBy('id');
        $packages = PackageManagement::whereIn('id', $request->package_ids ?? [])->get()->keyBy('id');

        // Get existing tests to prevent duplicates
        $existingTestIds = PrescriptionPackageMapping::where('prescription_id', $prescription->id)
            ->pluck('test_id')
            ->toArray();

        // Process catalogs
        foreach ($catalogs as $catalog) {
            if (!in_array($catalog->id, $existingTestIds)) {
                $prescriptionTestMapping[] = [
                    'prescription_id' => $prescription->id,
                    'test_id'        => $catalog->id,
                    'price'          => $catalog->price,
                    'type'           => 'test_case',
                    'start_at'       => null,
                    'end_at'         => null,
                    'is_discount'    => 0,
                    'discount_type'  => null,
                    'discount_price' => null
                ];
            }
        }

        // Process packages
        foreach ($packages as $package) {
            if (!in_array($package->id, $existingTestIds)) {
                $prescriptionTestMapping[] = [
                    'prescription_id' => $prescription->id,
                    'test_id'        => $package->id,
                    'price'          => $package->price,
                    'type'           => 'package',
                    'start_at'       => $package->start_at,
                    'end_at'         => $package->end_at,
                    'is_discount'    => $package->is_discount,
                    'discount_type'  => $package->discount_type,
                    'discount_price' => $package->discount_price
                ];
            }
        }

        // Bulk insert if new tests are added
        if ($prescriptionTestMapping) {
            PrescriptionPackageMapping::insert($prescriptionTestMapping);
        }

        // Handle lab association
        if ($request->lab_id) {
            $testIds = PrescriptionPackageMapping::where('prescription_id', $prescription->id)
                ->pluck('id')
                ->toArray();

            $existingLabMappings = PrescriptionLabMapping::where('prescription_id', $prescription->id)
                ->pluck('test_id')
                ->toArray();

            $prescriptionLabMapping = [];

            foreach ($testIds as $testId) {
                if (!in_array($testId, $existingLabMappings)) {
                    $existingMapping = PrescriptionLabMapping::where('prescription_id', $prescription->id)
                        ->where('lab_id', $request->lab_id)
                        ->whereNull('test_id')
                        ->first();

                    if ($existingMapping) {
                        // If found, update it
                        $existingMapping->update(['test_id' => $testId]);
                    } else {
                        // Otherwise, create a new record
                        PrescriptionLabMapping::create([
                            'prescription_id' => $prescription->id,
                            'lab_id'         => $request->lab_id,
                            'test_id'        => $testId,
                        ]);
                    }
                }
            }

        }

        return response()->json(['success' => true]);
    }

    
    public function removeTest(Request $request,$id)
    {
        $testmapping = PrescriptionPackageMapping::where('id',$id)->first();

        if ($testmapping) {
            $labMapping = PrescriptionLabMapping::where('test_id',$id)->first();
            $labId = $labMapping->lab_id;
    
            // Delete the mapping and test
            $testmapping->delete();
            $labMapping->delete();
    
            // Check if any mappings remain for the lab
            $remainingMappings = PrescriptionLabMapping::where('lab_id', $labId)->exists();
    
            return response()->json([
                'success' => true,
                'lab_empty' => !$remainingMappings, // Indicates if the lab is now empty
                'lab_id' => $labId
            ]);
        }
    
        return response()->json(['success' => false, 'message' => __('messages.test_not_found')], 404);
        
    }

    public function sendSuggestion(Request $request,$id)
    {
        $data = Prescription::MyPrescription()->where('id', $id)->first();
        if($data == null){
            return redirect()->route('backend.prescriptions.index')->with('error', __('messages.record_not_found'));
        }
        $data->note = $request->note;
        $data->prescription_status = '1';
        $data->save();

        $activity_data = [
            'activity_type' => 'prescription_suggestion',
            'notification_type' => 'prescription_suggestion', 
            'prescription_id' => $data->id,
            'prescription' => $data,
            'user_id' => $data->user_id,
        ];
        if ($request->is_notify) {
            $this->sendNotification($activity_data);
            return back()->with('success', __('messages.notification_sent'));
        }
        $this->sendNotification($activity_data);
        return redirect()->route('backend.prescriptions.index')->with('success', __('messages.prescription_suggestion_sent'));
    }
    
}

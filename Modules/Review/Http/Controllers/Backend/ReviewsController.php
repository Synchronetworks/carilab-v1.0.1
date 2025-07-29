<?php

namespace Modules\Review\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Review\Models\Review;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Review\Http\Requests\ReviewRequest;
use App\Trait\ModuleTrait;

class ReviewsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\ReviewExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'Reviews', // module title
            'reviews', // module name
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
        $type = 'reviews';
        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'collector',
                'text' => __('messages.lbl_collector'),
            ],
            [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
            ],
            [
                'value' => 'customer',
                'text' => __('messages.lbl_customer'),
            ],
            [
                'value' => 'rating',
                'text' => __('messages.lbl_rating'),
            ],
            [
                'value' => 'review',
                'text' => __('messages.review'),
            ]
        ];
        $export_url = route('backend.reviews.export');

        return view('review::backend.review.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url', 'type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'Review'; // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Review::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Review::query()->with('user', 'collector')->visibleToUser(auth()->user());

        $user = auth()->user();

       
        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($filter['lab_id'])) {
            $query->where('lab_id', $filter['lab_id']);
            if ($user && $user->hasRole('vendor')) {
                $query = $query->whereHas('lab', function ($query) use ($user) {
                    $query->where('vendor_id', $user->id);
                });
            }
        }
        if (isset($filter['collector_id'])) {
            $query->where('collector_id', $filter['collector_id']);
            if ($user && $user->hasRole('vendor')) {

                $query = $query->whereHas('collector', function ($query) use ($user) {
                    $query->where('user_type', 'collector')->whereHas('collectorVendormapping', function ($query) use ($user) {
                        $query->where('vendor_id', $user->id);
                    });
                });


            }
        }
        if (isset($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        }

        if (isset($filter['vendor_id'])) {
            $query->whereHas('lab', function ($qry) use ($filter) {
                $qry->whereHas('vendor', function ($q) use ($filter) {
                    $q->where('vendor_id', $filter['vendor_id']);
                });
            });
        }
        if (isset($filter['column_status'])) {
            $query = $filter['column_status'] == 'lab' ? $query->whereNotNull('lab_id') : $query->whereNotNull('collector_id');
        }
        return $datatable->eloquent($query)
            ->editColumn('name', fn($data) => $data->name)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" data-type="reviews" onclick="dataTableRowCheck(' . $data->id . ',this)">';
            })
            ->editColumn('user', function ($data) {
                $data = $data->user;
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
            ->orderColumn('user', function ($query, $direction) {
                $query->join('users', 'users.id', '=', 'reviews.user_id')
                    ->select('reviews.*', 'users.first_name', 'users.last_name') // Ensure names are selected
                    ->orderBy('users.first_name', $direction)
                    ->orderBy('users.last_name', $direction);
            })
            ->editColumn('lab', function ($data) {
                $data = $data->lab;
                return view('lab::backend.lab_details', compact('data'));
            })
            ->filterColumn('lab', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('lab', function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
                }
            })
            ->orderColumn('lab', function ($query, $order) {
                $query->join('labs', 'labs.id', '=', 'reviews.lab_id')
                    ->select('reviews.*', 'labs.name') // Ensure lab name is selected
                    ->orderBy('labs.name', $order);
            })
            ->editColumn('collector', function ($data) {
                $data = $data->collector;
                return view('user::backend.users.user_details', compact('data'));
            })
            ->filterColumn('collector', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('collector', function ($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', "%{$keyword}%")
                            ->orWhere('last_name', 'like', "%{$keyword}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"])
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
                }
            })
            ->editColumn('rating', function ($data) {
                return $data->rating ?? '-';
            })
            ->filterColumn('rating', function ($query, $keyword) {
                if (is_numeric($keyword)) {
                    $query->where('rating', '=', (int) $keyword);
                }
            })
            ->orderColumn('rating', function ($query, $direction) {
                $query->orderBy('rating', $direction);
            })



            ->orderColumn('collector', function ($query, $order) {
                $query->join('users as collectors', 'collectors.id', '=', 'reviews.collector_id')
                    ->select('reviews.*', 'collectors.first_name', 'collectors.last_name', 'collectors.email') // Select necessary columns
                    ->orderByRaw("CONCAT(collectors.first_name, ' ', collectors.last_name) $order")
                    ->orderBy('collectors.email', $order);
            })
            
            ->addColumn('action', function ($data) {
                return view('review::backend.review.action', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusLabelAttribute();
            })
            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'status', 'check'])
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
        $module_title=__('messages.new_review');
      return view('review::backend.review.create',compact('module_title'));
    }

    public function store(ReviewRequest $request)
    {
        $data = $request->all();
        $review = Review::create($data);
        $this->logActivity('create', $review, 'review_create');
        $message = __('messages.record_add');
        return redirect()->route('backend.reviews.index', $review->id)->with('success', $message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Review::findOrFail($id);
        return view('review::backend.review.edit', compact('data'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ReviewRequest $request, Review $review)
    {
        $requestData = $request->all();
        $review->update($requestData);
        $this->logActivity('update', $review, 'review_update');
        $message = __('messages.update_form');
        return redirect()->route('backend.reviews.index', $review->id)->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Review::findOrFail($id);
        $data->delete();
        $this->logActivity('delete', $data, 'review_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' => $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Review::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore', $data, 'review_restore');
        $message = __('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Review::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete', $data, 'review_force_delete');
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }
}

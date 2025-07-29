@extends('backend.layouts.app')
@section('title', __('messages.prescriptions_details'))

@section('content')
<div class="form-content"> 
    <div>
        <x-back-button-component route="backend.prescriptions.index" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{__('messages.lbl_customer')}} {{__('messages.details')}}</h4>
        <div class="mt-2 d-flex gap-2">
            @if($data->getFirstMedia('prescription_upload'))
                <a href="{{ $data->getFirstMediaUrl('prescription_upload') }}" 
                target="_blank" 
                class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> {{__('messages.view_document')}}
                </a>
            @endif
            <a href="{{ route('backend.prescriptions.download_document', ['id' => $data->id]) }}" 
                class="btn btn-secondary btn-sm">
                <i class="fas fa-download"></i> {{__('messages.download_document')}}
            </a>
        </div>
    </div>

    
    <div class="card">
        <div class="card-body">                    
            <div class="d-flex gap-3 align-items-center">
                <img src="{{ optional($data->user)->profile_image ?? default_user_avatar() }}"
                    alt="avatar" class="avatar avatar-70 rounded-pill">
                <div class="text-start">
                    <a href="{{ route('backend.users.details', ($data->user)->id) }}" >
                    <h5 class="mb-2">{{ optional($data->user)->full_name ?? default_user_name() }}
                    </h5>
                    </a>
                    <span>{{ optional($data->user)->email ?? '--' }}</span>
                </div>
            </div>  
        </div>
    </div>

    <h4 class="mb-3"> {{__('messages.other_info')}} </h4>
    <div class="card">
        <div class="card-body">
            <form id="labSelectionForm">
                @csrf
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-3 flex-grow-1">
                        <!-- Lab Selection -->
                        <div class="flex-grow-1">
                            <label class="form-label" for="lab_id">{{__('messages.select_lab')}}<span class="text-danger">*</span></label>
                            <select name="lab_id" id="lab_id" class="form-control select2" required>
                                <option value="">{{__('messages.select_lab')}}</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}" {{ ($data['lab_id'] == $lab->id) ? 'selected' : '' }}>
                                        {{ $lab->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Catalogs Selection -->
                        <div class="flex-grow-1">
                            <label class="form-label" for="catalog_ids">{{__('messages.lbl_test_case')}}<span class="text-danger">*</span></label>
                            <select name="catalog_ids[]" id="catalog_ids" class="form-control select2" multiple data-placeholder="{{__('messages.lbl_test_case')}}" required>
                            </select>
                        </div>

                        <!-- Packages Selection -->
                        <div class="flex-grow-1">
                            <label class="form-label" for="package_ids">{{__('messages.select_packages')}}</label>
                            <select name="package_ids[]" id="package_ids" class="form-control select2" multiple data-placeholder="{{__('messages.select_packages')}}">
                            </select>
                        </div> 
                    </div>                                           

                    <!-- Submit Button -->
                    <div class="flex-shrink-0 align-self-end">
                        <button type="submit" class="btn btn-primary" id="add-section" onclick="disableButton()">
                        {{__('messages.add_selection')}}
                        </button>
                    </div>
                </div>
            </form>
            <!-- Display Selected Labs and Their Items -->
            @if($prescriptionLabs !== null)
                <div class="selected-labs-container mt-4">
                    @foreach($prescriptionLabs->groupBy('lab_id') as $labId => $labMappings)
                    @php
                        // Filter out lab mappings where test_id is NULL
                        $validTests = $labMappings->filter(fn($labMapping) => $labMapping->test_id !== null);
                    @endphp

                    @if($validTests->isNotEmpty()) 
                        <div class="card mb-4 bg-body" id="lab-{{ $labId }}">
                                <div class="card-body">
                                    <h5 class="mb-4">{{ $labMappings->first()->lab->name ?? 'No Lab' }}</h5>
                                <!-- Lab Details Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('messages.lbl_type') }}</th>
                                                <th>{{ __('messages.lbl_test_case') }} {{ __('messages.lbl_name') }}</th>
                                                <th>{{ __('messages.lbl_price') }}</th>
                                                <th>{{ __('messages.lbl_discount') }}</th>
                                                <th>{{ __('messages.lbl_action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($labMappings as $labMapping)
                                                <!-- Test Row -->
                                                @if($labMapping->test_mapping && $labMapping->test_mapping instanceof \Modules\CatlogManagement\Models\CatlogManagement)
                                                    <tr id="row-{{ $labMapping->testMapping->id }}">
                                                        <td>{{__('messages.lbl_test')}}</td>
                                                        <td>{{ $labMapping->test_mapping->name }}</td>
                                                        <td>{{ \Currency::format($labMapping->test_mapping->price ?? 0) }}</td>
                                                        <td>-</td>
                                                        
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-test" 
                                                                data-id="{{ $labMapping->testMapping->id }}">
                                                                {{__('messages.remove')}}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
            
                                                <!-- Package Row -->
                                                @if($labMapping->test_mapping && $labMapping->test_mapping instanceof \Modules\PackageManagement\Models\PackageManagement)
                                                    <tr id="row-{{ $labMapping->testMapping->id }}">
                                                        <td>{{__('messages.package')}}</td>
                                                        <td>{{ $labMapping->test_mapping->name }}</td>
                                                        <td>{{ \Currency::format($labMapping->test_mapping->price ?? 0) }}</td>
                                                        <td>{{ $labMapping->test_mapping->is_discount 
        ? ($labMapping->test_mapping->discount_type == 'fixed' 
            ? \Currency::format($labMapping->test_mapping->discount_price) 
            : $labMapping->test_mapping->discount_price . ' %') 
        : '-' }}</td>
                                                        
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-test" 
                                                                data-id="{{ $labMapping->testMapping->id }}">
                                                                {{__('messages.remove')}}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    <h4 class="mb-3"> {{__('messages.note')}} </h4>
    <div class="card">
        <div class="card-body">
           <!-- Note and Submit Form -->
            <form id="prescriptionForm" action="{{ route('backend.prescriptions.send_suggestion', $data->id) }}" method="POST">
                @csrf
                <!-- Note Section -->
                <div class="form-group mb-3">
                    <label class="form-label" for="note">{{__('messages.describe_in_detail')}}</label>
                    <textarea name="note" id="note" class="form-control" rows="4" 
                        placeholder="{{__('messages.add_prescription_suggestion')}}"></textarea>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-paper-plane"></i>{{__('messages.send_prescription')}}
                </button>                   
            </form>
        </div>
    </div>
    
        
        


</div>



@push('after-scripts')

<script>
    function disableButton(){
document.getElementById('add-section').classList.add('disabled');
document.getElementById('add-section').innerText = 'Adding Selection ...';
}
$(document).ready(function() {
    // Form Submit
    $('#labSelectionForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "{{ route('backend.prescriptions.add_selection', $data->id) }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    location.reload();
                }
            }
        });
    });

    // Remove Catalog
    $(document).on('click', '.remove-test', function(event) {
        event.preventDefault();

        let id = $(this).data('id'); // Get the ID of the test
        const URL = "{{ route('backend.prescriptions.remove_test', '') }}/" + id; // Construct the URL

        Swal.fire({
            title: "{{ __('messages.are_you_sure_remove_test') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{ __('messages.yes_remove_it') }}",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: URL,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}", // CSRF token for security
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Remove the row of the deleted test
                            $(`#row-${id}`).remove();

                            // If the lab is now empty, remove the entire lab table
                            if (response.lab_empty) {
                                $(`#lab-${response.lab_id}`).remove();
                            }

                            Swal.fire({
                                title: "{{ __('messages.success') }}",
                                text: "{{ __('messages.test_removed_successfully') }}",
                                icon: "success"
                            });
                        } else {
                            Swal.fire({
                                title: "{{ __('messages.error') }}",
                                text: "{{ __('messages.failed_to_remove_the_test') }}",
                                icon: "error"
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: "{{ __('messages.error') }}",
                            text: "{{ __('messages.something_went_wrong') }}",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });



});
   // Lab Change Event
     

$(document).ready(function() {
    let labId = $('#lab_id').val();
    
    if (labId) {
        $('#catalog_ids, #package_ids').prop('disabled', false);
        loadCatalogs(labId);
        loadPackages(labId);
    }

    $('#lab_id').on('change', function() {
        let labId = $(this).val();
        if (labId) {
            $('#catalog_ids, #package_ids').prop('disabled', false);
            loadCatalogs(labId);
            loadPackages(labId);
        } else {
            $('#catalog_ids, #package_ids').prop('disabled', true).empty();
            $('#selectedCatalogsTable tbody, #selectedPackagesTable tbody').empty();
            $('#catalogTotal, #packageTotal').text('0');
        }
    });

    function loadCatalogs(labId) {
        $.ajax({
            url: "{{ route('backend.catlogmanagements.index_list') }}",
            type: "GET",
            data: { lab_id: labId },
            success: function(response) {
                $('#catalog_ids').empty();
                $.each(response, function(key, value) {
                    $('#catalog_ids').append(`
                        <option value="${value.id}" data-price="${value.price}">
                            ${value.name}
                        </option>
                    `);
                });
            }
        });
    }

    function loadPackages(labId) {
        $.ajax({
            url: "{{ route('backend.packagemanagements.index_list') }}",
            type: "GET",
            data: { lab_id: labId },
            success: function(response) {
                $('#package_ids').empty();
                $.each(response, function(key, value) {
                    let finalPrice = value.has_discount ? value.discount_price : value.price;
                    $('#package_ids').append(`
                        <option value="${value.id}" 
                                data-price="${value.price}"
                                data-discount="${value.has_discount ? value.discount_price : ''}"
                                data-final-price="${finalPrice}">
                            ${value.name}
                        </option>
                    `);
                });
            }
        });
    }
});

</script>
@endpush
@endsection

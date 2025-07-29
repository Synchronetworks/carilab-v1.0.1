@extends('backend.layouts.app')
@section('title'){{__($module_action)}} {{__($module_title)}}@endsection
@section('content')
<div class="form-content">
    {{ html()->form('POST', route('backend.' . request()->segment(2) . '.update', $data->id))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
        @csrf
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div class="card-input-title">
                <h4 class="m-0">{{ __('messages.basic_information') }}</h4>
            </div>
            <a href="{{ ($data->user_type == 'collector') ? route('backend.collector_bank.index') : route('backend.vendor_bank.index')  }}" class="btn btn-sm btn-primary">
                <i class="ph ph-caret-double-left"></i> {{ __('messages.back') }}
            </a>
        </div> 
        <div class="card">
            <div class="card-body">  
                @method('PUT')
                {{ html()->hidden('id',$data->id ?? null) }}
                <div class="row gy-4">
                    <!-- Bank Name -->
                    <div class="col-sm-6">
                        {{ html()->label(__('messages.lbl_bank_name') . ' <span class="text-danger">*</span>', 'bank_name')->class('form-label') }}
                        {{ html()->text('bank_name')
                                ->attribute('value', $data->bank_name)  ->placeholder(__('messages.placeholder_bank_name'))
                                ->class('form-control')
                            }}
                        @error('bank_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Branch Name -->
                    <div class="col-sm-6">
                        {{ html()->label(__('messages.lbl_branch_name') . ' <span class="text-danger">*</span>', 'branch_name')->class('form-label') }}
                        {{ html()->text('branch_name')
                                ->attribute('value', $data->branch_name)  ->placeholder(__('messages.placeholder_branch_name'))
                                ->class('form-control')
                            }}
                        @error('branch_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-sm-6">
                        {{ html()->label(__('messages.lbl_ifsc_code') . ' <span class="text-danger">*</span>', 'ifsc_code')->class('form-label') }}
                        {{ html()->text('ifsc_code')
                                ->attribute('value', $data->ifsc_code)
                                ->placeholder(__('messages.placeholder_ifsc_code'))
                                ->class('form-control')
                                ->required()
                            }}
                        @error('ifsc_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Account Number -->
                    <div class="col-sm-6">
                        {{ html()->label(__('messages.lbl_account_no') . ' <span class="text-danger">*</span>', 'account_no')->class('form-label') }}
                        {{ html()->text('account_no')
                                ->attribute('value', $data->account_no)  ->placeholder(__('messages.placeholder_Account_number'))
                                ->class('form-control')
                            }}
                        @error('account_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mobile Number -->
                    <div class="col-sm-6">
                        {{ html()->label(__('messages.lbl_phone_number') . ' <span class="text-danger">*</span>', 'phone_number')->class('form-label') }}
                        {{ html()->input('tel', 'phone_number')
                                ->attribute('value', $data->phone_number)  ->placeholder(__('messages.lbl_phone_number'))
                                ->class('form-control')
                                ->required()
                            }}
                        @error('phone_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                   <!-- user_id  -->
                   @if(
                        ($data->user_type == 'collector' && auth()->user()->hasRole(['admin', 'demo_Admin', 'vendor'])) || 
                        ($data->user_type == 'vendor' && auth()->user()->hasRole(['admin', 'demo_admin']))
                    )
                    <div class="col-sm-6">
                        {{ html()->label(__($data->user_type == 'collector' ? 'messages.lbl_collector' : 'messages.lbl_vendor')  . ' <span class="text-danger">*</span>', 'user_id')->class('form-label') }}
                        <select name="user_id" class="form-select select2" required>
                            <option value="">{{ __('messages.select_name', ['select' => __($data->user_type == 'collector' ? 'messages.collector' : 'messages.vendor')]) }}</option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                    {{ old('user_id', $data->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('user_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif
                @if(auth()->user()->hasRole('vendor') && $data->user_type == 'vendor')
                <input type='hidden' value='{{auth()->id()}}' name='user_id' class='form-label'>
                @endif
                    <!-- Status -->
                    <div class="col-sm-6">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                        <div class="d-flex align-items-center justify-content-between form-control">
                            <label for="status" class="form-label mb-0 text-body">{{ __('messages.active') }}</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="status" value="0"> <!-- Hidden input field -->
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $data->status == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                </div>     
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-end">
            {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary') }}   
        </div>
        {{ html()->form()->close() }}
        
</div>
@endsection

@push('after-scripts')
<script>
   
    document.getElementById('phone_number').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endpush

<div class="d-flex gap-3 align-items-center justify-content-end">
@php
    $payment_status_check =   Modules\Appointment\Models\CashPaymentHistories::where('transaction_id',$data->id)->orderBy('id','desc')->first();
@endphp

@if($payment_status_check !== null && $payment_status_check->status == 'send_to_vendor' && auth()->user()->hasRole('vendor')) 
    <a class="btn-sm text-white btn btn-success me-2"  href="{{route('backend.payments.approve',$data->id)}}"><i class="fa fa-check"></i>{{__('messages.approve')}}</a>
@elseif($payment_status_check !== null && $payment_status_check->status == 'approved_by_vendor' && auth()->user()->hasRole('vendor')) 
    <a class="btn-sm text-white btn btn-success me-2"  href="{{route('backend.payments.approve',$data->id)}}"><i class="fa fa-check"></i>{{__('messages.send')}}</a>
@elseif($payment_status_check !== null && $payment_status_check->status == 'send_to_admin' && auth()->user()->hasRole('admin')) 
    <a class="btn-sm text-white btn btn-success me-2"  href="{{route('backend.payments.approve',$data->id)}}"><i class="fa fa-check"></i>{{__('messages.approve')}}</a>
@endif


@if(auth()->user()->hasAnyRole(['admin']))
    @if(!$data->trashed())
    
    <a class="fs-4 delete-tax text-danger" href="{{ route('backend.payments.destroy', $data->id) }}">
        <i class="ph ph-trash align-middle"></i>
    </a>
    @else
  
    <a class="fs-4 text-info restore-tax" href="{{ route('backend.payments.restore', $data->id) }}">
        <i class="ph ph-arrow-clockwise align-middle"></i>
    </a>

    <a class="fs-4 force-delete-tax text-danger" href="{{ route('backend.payments.force_delete', $data->id) }}">
        <i class="ph ph-trash align-middle"></i>
    </a>
    @endif
@endif
</div>


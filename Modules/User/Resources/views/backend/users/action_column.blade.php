<div class="d-flex gap-3 align-items-center justify-content-end">
@if(!$data->trashed())
    <a class="text-success fs-4" data-bs-toggle="tooltip" title="{{ __('messages.edit') }}"
        href="{{ $data->user_type == 'customer' ? route('backend.customer.edit', $data->id) : route('backend.users.edit', $data->id) }}">
        <i class="ph ph-pencil-simple-line align-middle"></i>
    </a>
    <a class="text-info fs-4" href="{{ ($data->user_type == 'collector') ? route('backend.collectors.details', $data->id) :route('backend.users.details', $data->id) }}"
        data-bs-toggle="tooltip" title="{{ __('messages.detail') }}">
        <i class="ph ph-eye align-middle"></i>
    </a>
    @if ($data->login_type != 'google')
        <a class="text-warning fs-4" data-bs-toggle="tooltip"
            title="{{ __('messages.change_password') }}" href="{{ route('backend.users.changepassword', $data->id) }}">
            <i class="ph ph-lock align-middle"></i>
        </a>
    @endif
    <!-- Soft Delete (Trash) -->
    <a href="{{ route('backend.users.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
        class="text-danger fs-4" data-type="ajax" data-method="DELETE"
        data-token="{{ csrf_token() }}" data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
        data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash align-middle"></i></a>
@else
@hasPermission('restore_plans')
            <a class="text-info fs-4 restore-tax" data-confirm-message="{{__('messages.are_you_sure_restore')}}" 
    data-success-message="{{__('messages.restore_form',  ['form' => 'Plan'])}}" data-bs-toggle="tooltip" title="{{__('messages.restore')}}" href="{{ route('backend.users.restore', $data->id) }}">
                <i class="ph ph-arrow-clockwise align-middle"></i>
            </a>
@endhasPermission
@hasPermission('force_delete_plans')
            <a href="{{route('backend.users.force_delete', $data->id)}}" id="delete-locations-{{$data->id}}" class="text-danger fs-4" data-type="ajax" data-method="DELETE" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('messages.force_delete')}}" data-confirm="{{ __('messages.are_you_sure?') }}"> <i class="ph ph-trash align-middle"></i></a>
            @endhasPermission
@endif            
</div>

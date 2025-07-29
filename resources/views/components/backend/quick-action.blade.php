<form action="{{$url ?? ''}}" id="quick-action-form" class="form-disabled d-flex flex-wrap gap-3 align-items-stretch flex-grow-1 table-action-left">
  @csrf
  {{$slot}}
  <input type="hidden" name="message_change-featured" value="{{__('messages.are_you_sure_want_to_perform_this_action')}}">
  <input type="hidden" name="message_change-status" value="{{__('messages.are_you_sure_want_to_perform_this_action')}}">
  <input type="hidden" name="message_delete" value="{{__('messages.are_you_sure_want_to_delete_it')}}">
  <input type="hidden" name="message_restore" value="{{__('messages.are_you_sure_want_to_restore_it')}}">
  <input type="hidden" name="message_permanently-delete" value="{{__('messages.are_you_sure_want_to_delete_it')}}">
  <button class="btn btn-primary" id="quick-action-apply">{{ __('messages.apply') }}</button>
</form>

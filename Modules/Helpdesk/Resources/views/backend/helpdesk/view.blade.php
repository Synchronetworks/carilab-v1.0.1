@extends('backend.layouts.app')
@section('title'){{__('messages.help_desks')}} {{__('messages.details')}} @endsection

@section('content')
<x-back-button-component route="backend.helpdesks.index" />

<main class="main-area">
    <div class="main-content">
        <div class="container-fluid">
            
            <div class="card">
                <div class="card-body p-30">
                    <div class="helpdesk-details-overview row gy-4">
                        <div class="helpdesk-details-overview__statistics col-lg-5">
                            <div class="statistics-card statistics-card__style2 statistics-card__order-overview">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th width="25%">{{ __('messages.id') }}:</th>
                                            <td class="text-wrap">#{{ $helpdeskdata->id ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="25%">{{ __('messages.datetime') }}:</th>
                                            <td class="text-wrap">{{  $datetime ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="25%">{{ __('messages.name') }}:</th>
                                            <td class="text-wrap">
                                                {{ $helpdeskdata->user_id ? optional($helpdeskdata->users)->first_name . ' ' . optional($helpdeskdata->users)->last_name . ' (' . ucfirst(optional($helpdeskdata->users)->user_type) . ')' : '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="25%">{{ __('messages.mode') }}:</th>
                                            <td class="text-wrap">
                                                {{ $helpdeskdata->mode ? ucfirst($helpdeskdata->mode) : '-' }}
                                                @if($helpdeskdata->mode === 'phone')
                                                    <span> ({{ $helpdeskdata->contact_number ?? optional($helpdeskdata->users)->contact_number }})</span>
                                                @elseif($helpdeskdata->mode === 'email')
                                                    <span> ({{ $helpdeskdata->email ?? optional($helpdeskdata->users)->email }})</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>

                            <h5 class="mb-2 mt-4">{{ __('messages.help_desks') }} {{ __('messages.detail') }}</h5>
                            <div class="statistics-card statistics-card__style2 statistics-card__order-overview">
                                <span class="helpdesk-status">
                                    @if($helpdeskdata->status == '0')
                                        <span class="badge text-white bg-success text-uppercase">{{ __('messages.open') }}</span>
                                    @else
                                        <span class="badge text-white bg-danger text-uppercase">{{ __('messages.closed') }}</span>
                                    @endif
                                </span>
                                <table class="table table-bordered mb-1">
                                    <tr>
                                        <td width="25%"><span>{{ __('messages.subject') }} : </span></td>
                                        <td class=""><strong>{{ !empty($helpdeskdata->subject) ? $helpdeskdata->subject : '-' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><span>{{ __('messages.description') }} : </span></td>
                                        <td>
                                            <div class="d-flex gap-2 align-items-center">
                                                @php
                                                    $attachmentUrls = getAttachments($helpdeskdata->getMedia('helpdesk_attachment'));
                                                    $firstAttachmentUrl = $attachmentUrls[0] ?? null; // Use a default image if empty
                                                @endphp
                                                @if($firstAttachmentUrl !== null)
                                                <img src="{{ $firstAttachmentUrl }}" alt="avatar" class="avatar avatar-40"  data-bs-toggle="modal" data-bs-target="#imageModal">   
                                                @endif             
                                                <div class="text-start">
                                                    <span class="">{{ $helpdeskdata->description ?? '--' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>                                      
                            </div>
                        </div>
                      
                        <div class="rounded-2 helpdesk-note col-lg-7">
                            <h5 class="mb-1 ">{{ __('messages.note') }}</h5>
                            <div class="statistics-card statistics-card__order-overview">
                                @if( count($helpdeskdata->helpdeskactivity) <= 1 && $helpdeskdata->status == 0)

                            {{ html()->form('POST', route('backend.helpdesks.activity', $helpdeskdata->id))
                                ->attribute('enctype', 'multipart/form-data')
                                ->attribute('data-toggle', 'validator')
                                ->id('helpdeskactivty-form')
                                ->open()
                            }}
                            {{ html()->hidden('helpdesk_id', $helpdeskdata->id ?? null) }}
                            @csrf
                            <div>
                            <div class="row gy-4">
                                    <!-- File input for attachments -->
                                    <div class="form-group col-md-12">
                                        <label class="form-label" for="helpdesk_activity_attachment">{{ __('messages.image') }}</label>
                                        <div class="custom-file">
                                            <input type="file" name="helpdesk_activity_attachment[]" class="custom-file-input form-control"
                                                data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*">
                                        </div>
                                    </div>

                                    <!-- Description text area -->
                                    <div class="form-group col-md-12">
                                        {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-label') }}
                                        <textarea name="description" class="form-control textarea" required rows="3" placeholder="{{ __('messages.description') }}"></textarea>

                                    </div>
                                </div>

                                <div class="modal-footer row-gap-3 column-gap-2 mt-4">
                                    <!-- Link to mark as closed -->
                                    <a href="{{ route('backend.helpdesks.closed', ['id' => $helpdeskdata->id]) }}" class="btn btn-md btn-secondary">{{ __('messages.marked_closed') }}</a>

                                    <!-- Submit button with id -->
                                    {{ html()->submit( __('messages.save'))->class('btn btn-md btn-primary')->id('replyButton') }}
                                    
                                </div>
                                </div>
                                {{ html()->form()->close() }}
                              
                                @elseif(count($helpdeskdata->helpdeskactivity) > 0)
                             
                                        <div class="activity-height">
                                            <ul class="iq-timeline provider-timeline">
                                                <?php date_default_timezone_set($admin->time_zone ?? 'UTC'); ?>
                                             
                                                @foreach($helpdeskdata->helpdeskactivity as $index => $activity)
                                               
                                                <li>
                                                    <div class="timeline-dots">
                                                
                                                        <img src="{{ getSingleMedia(optional($activity->sender),'profile_image', null) }}" alt="avatar" class="avatar avatar-40 rounded-pill d-block"> 
                                                    </div>
                                                    @if($activity->activity_type !== 'closed_helpdesk')
                                                    <div class="d-flex justify-content-between flex-sm-nowrap flex-wrap gap-2 timeline-content">                                    
                                                        <span class="mb-1">
                                                           
                                                            {{ __($index === 0 ? 'messages.created_by_helpdesk' : 'messages.replied_by_helpdesk', [
                                                                'name' => optional($activity->sender)->full_name,
                                                                'date' => $activity->updated_at->format('Y-m-d H:i:s'),
                                                            ]) }}
                                                        </span>
                                                        <h6 class="mb-1 text-primary toggle-message" id="open-message-{{ $index }}" style="cursor: pointer;">{{__('messages.show_message')}} <i class="ph ph-caret-down toggle-icon"></i></h6>
                                                    </div>
                                                    
                                                    <div class="d-flex gap-2 align-items-md-center flex-md-row flex-column message-content d-none" id="messages-{{ $index }}" >
                                                        @php
                                                            $attachmentUrls = $index === 0 ? getAttachments($helpdeskdata->getMedia('helpdesk_attachment')) : getAttachments($activity->getMedia('helpdesk_activity_attachment'));
                                                            $firstAttachmentUrl = $attachmentUrls[0] ?? null; // Use a default image if empty
                                                        @endphp
                                                        @if($firstAttachmentUrl !== null)
                                                            <img src="{{ $firstAttachmentUrl }}" alt="avatar" class="avatar avatar-40" data-bs-toggle="modal" 
                                                            data-bs-target="#imageModal">
                                                        @endif
                                                            <div class="text-start" id="messages">
                                                                <span>{{ $activity->messages ?? '--' }}</span>
                                                            </div>
                                                    </div>
                                                    @elseif($activity->activity_type == 'closed_helpdesk')
                                                    <div class="d-flex justify-content-between gap-2">
                                                    <span class="mb-1">
                                                        {{ __('messages.closed_by_helpdesk', [
                                                        'name' => optional($activity->sender)->full_name,
                                                        'date' => $activity->updated_at->format('Y-m-d H:i:s')??'-',
                                                        ])}}
                                                    </span>
                                                    </div>
                                                    @endif
                                                </li>
                                                @endforeach
                                                
                                            </ul>
                                            {{ html()->form('POST', route('backend.helpdesks.activity', $helpdeskdata->id))
                                                ->attribute('enctype', 'multipart/form-data')
                                                ->attribute('data-toggle', 'validator')
                                                ->id('replyForm')
                                                ->style("display: none;")
                                                ->open()
                                            }}
                                            {{ html()->hidden('helpdesk_id', $helpdeskdata->id ?? null) }}
                                                @csrf
                                                <div class="row gy-4 mt-3">
                                                    <!-- File input for attachments -->
                                                    <div class="form-group col-md-12">
                                                        <label class="form-label" for="helpdesk_activity_attachment">{{ __('messages.image') }}</label>
                                                        <div class="custom-file">
                                                            <input type="file" name="helpdesk_activity_attachment[]" class="custom-file-input form-control"
                                                                data-file-error="{{ __('messages.files_not_allowed') }}" accept="image/*">
                                                        </div>
                                                    </div>

                                                    <!-- Description text area -->
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(trans('messages.description'). ' <span class="text-danger">*</span>', 'description')->class('form-label') }}
                                                        <textarea name="description" class="form-control textarea" required rows="3" placeholder="{{ __('messages.description') }}"></textarea>

                                                    </div>
                                                </div>

                                                <!-- Link to mark as closed and submit button -->
                                                <div class="modal-footer">
                                                    <a href="{{ route('backend.helpdesks.closed', ['id' => $helpdeskdata->id]) }}" class="btn btn-md btn-secondary mx-4">{{ __('messages.marked_closed') }}</a>
                                                    
                                                    {{ html()->submit( __('messages.reply'))->class('btn btn-md btn-secondary') }}
                                
                                                </div>
                                            {{ html()->form()->close() }}
                                            @if($helpdeskdata->status == 0)
                                            <div class="modal-footer mt-2">
                                                <button class="btn btn-md btn-primary" id="replyButton">{{ __('messages.reply') }}</button>
                                            </div>
                                            @endif
                                        </div>
                                @endif
                            </div>
                        </div>
                         <!-- Bootstrap Modal -->
                           
                            <div class="modal fade imageModal" id="imageModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imagePreviewLabel">{{__('messages.image_preview')}}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <img id="modalImage" class="img-fluid rounded-5">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
@push('after-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const replyButton = document.getElementById('replyButton');

    // Check if the button exists to avoid errors if the element is not rendered
    if (replyButton) {
        replyButton.addEventListener('click', function() {
            const replyForm = document.getElementById('replyForm');
            if (replyForm.style.display === 'none' || replyForm.style.display === '') {
                replyForm.style.display = 'block';
                replyButton.innerText = {{ __('messages.close') }};  // Change button text to "Close"
            } else {
                replyForm.style.display = 'none';
                replyButton.innerText = {{ __('messages.reply') }}  ;  // Change button text back to "Reply"
            }
        });
    }
    const images = document.querySelectorAll('img[data-bs-target="#imageModal"]');
        const modalImage = document.getElementById('modalImage');

        images.forEach(img => {
            img.addEventListener('click', function() {
                modalImage.src = this.src;
            });
        });

    document.querySelectorAll('.toggle-message').forEach(function (toggleButton) {
        toggleButton.addEventListener('click', function () {
            // Find the current index to toggle
            const index = this.id.replace('open-message-', '');
            const messageContent = document.getElementById(`messages-${index}`);
            const icon = this.querySelector('.toggle-icon');

            // Check if the messageContent element exists
            if (messageContent) {
                // Toggle the 'd-none' class to show/hide content
                messageContent.classList.toggle('d-none');
                
                // Update icon based on visibility
                if (messageContent.classList.contains('d-none')) {
                    icon.classList.remove('fa-angle-up');
                    icon.classList.add('fa-angle-down');
                } else {
                    icon.classList.remove('fa-angle-down');
                    icon.classList.add('fa-angle-up');
                }
            } else {
                console.warn(`Element with ID messages-${index} not found.`);
            }
        });
    });
});


 


</script>

@endpush
<style>
  #imageModal .modal-dialog {
    max-width: 90vw; /* Make modal wider to fit the image */
    margin: 0 auto;
  }
  #imageModal .modal-body {
    text-align: center;
    padding: 0; /* Remove padding to maximize space */
  }
  #modalImage {
    max-height: 90vh; /* Prevent image overflow */
    max-width: 100%; /* Ensure it fits within the modal */
    width: auto;
    height: auto;
    padding: 24px;
  }
  body:has(.imageModal.show)  .uploadModal{
    opacity: 0;
}
#imageModal {
  z-index: 2051;
}
</style>
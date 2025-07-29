@props(["toolbar"=>"", "subtitle"=>""])

<div class="d-flex justify-content-between flex-column flex-lg-row gap-3">
    {{ $slot }}
    @if($toolbar != "")
    <div class="btn-toolbar gap-3 align-items-center justify-content-lg-end table-action-right" role="toolbar" aria-label="Toolbar with buttons">
        {{ $toolbar }}
    </div>
    @endif
</div>

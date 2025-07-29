@if (setting('google_analytics') !== "")
@php
$google_analytics = setting('google_analytics');
@endphp
<!-- Google tag (gtag.js) -->

<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $google_analytics }}');
</script>
@endif

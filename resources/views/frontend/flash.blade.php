@if (session('success'))
<script>
    AIZ.plugins.notify('success', "{{ translate('Please login first') }}");
</script>
@endif
@if (session('danger'))
<script>
    AIZ.plugins.notify('danger', "{{ translate('Please login first') }}");
</script>
@endif
@if (session('warning'))
<script>
    AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
</script>
@endif
@if (session('info'))
<script>
    AIZ.plugins.notify('info', "{{ translate('Please login first') }}");
</script>
@endif
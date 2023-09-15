@if (session('success'))
<script>
    AIZ.plugins.notify('success', "{{ session()->get('success') }}");
</script>
@endif
@if (session('danger'))
<script>
    AIZ.plugins.notify('danger', "{{ session()->get('danger') }}");
</script>
@endif
@if (session('warning'))
<script>
    AIZ.plugins.notify('warning', "{{ session()->get('warning') }}");
</script>
@endif
@if (session('info'))
<script>
    AIZ.plugins.notify('info', "{{ session()->get('info') }}");
</script>
@endif
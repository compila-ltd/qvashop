@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Staffs')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg" width="10%">#</th>
                    <th>{{ translate('Name')}}</th>
                    <th data-breakpoints="lg">{{ translate('Email')}}</th>
                    <th data-breakpoints="lg">{{ translate('Phone')}}</th>
                    <th data-breakpoints="lg">{{ translate('Role')}}</th>
                    <th width="10%">{{ translate('Active')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $key => $staff)
                    @if($staff->user != null)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{$staff->user->name}}</td>
                            <td>{{$staff->user->email}}</td>
                            <td>{{$staff->user->phone}}</td>
                            <td>
								@if ($staff->role != null)
									{{ $staff->role->getTranslation('name') }}
								@endif
							</td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_commercial_active(this)" value="{{ $staff->id }}" type="checkbox" <?php if ($staff == $active_staff) echo "checked"; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function update_commercial_active(el) {
        if (el.checked) {
            $('.aiz-switch input[type="checkbox"]').prop('checked', false);
            $(el).prop('checked', true);
            $.post("{{ route('commercial_activation.activate') }}", {
                _token: '{{ csrf_token() }}',
                id: el.value,
                active: el.checked ? 1 : 0
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', "{{ translate('This commercial is active now') }}");
                } else if (data == 0) {
                    AIZ.plugins.notify('danger', "{{ translate('Something went wrong') }}");
                } 
            });
        } else {
            el.checked = true;
        }
    }
</script>
@endsection
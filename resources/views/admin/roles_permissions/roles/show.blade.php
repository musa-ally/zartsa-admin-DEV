@extends('components.master')
@section('title')
    Role
@endsection
@section('content')
    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
        <a href="{{ url()->previous() }}"><span class="badge bg-dark">⬅️ &nbsp;Go back</span></a>
    </div>
    <div class="row gutters-sm mt-3">
        <div class="mb-3">
            <div class="card">
                <div class="card-body">
                    <h4>Role name: {{ $name }}</h4>
                    <?php $info_body = 'Checked permission indicates that a role currently has that permission.<br>
                        To give this permission a role, just check a permission box and to remove permission to this role simply uncheck the permission box.' ?>
                    @include('components.info')
                    <h6 class="d-flex align-items-center mb-3">Role Permissions</h6>
                    <div class="row mb-4">
                        @foreach($permissions as $permission)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="checkbox" id="{{ $permission->id }}" @if(in_array($permission->id, json_decode($roles), true)) checked @endif
                                    onchange="addRemovePermission(this.id)">
                                    <label for="{{ $permission->id }}">{{ $permission->display_name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const roleId = '{{ $roleId }}';
        const permission_url = '{{ route('permission.add_remove') }}';
    </script>
@endsection

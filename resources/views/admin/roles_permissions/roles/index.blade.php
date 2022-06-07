<div class="box-container">
    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('create_role'))
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <button class="btn btn-primary" type="button"
                    data-mdb-toggle="modal" data-mdb-target="#roleModal1">Create new role</button>
        </div>
    @endif
    <div class="table-header-container mt-2 mb-1">
        <div class="row">
            <div class="col">
                <p>List of available roles</p>
            </div>
            <div class="col input-group justify-content-md-end mb-2">
                <div class="form-outline">
                    <input type="search" id="role_query" class="form-control" name="name"/>
                    <label class="form-label" for="form1">Search by role name</label>
                </div>
                <button type="button" class="btn btn-primary" onclick="searchRole()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Role name</th>
            <th scope="col">Role description</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody id="roleBody">
        <?php $num = 1 ?>
        @foreach($roles as $role)
            <tr>
                <th scope="row">{{ $num++ }}</th>
                <td>{{ $role->display_name }}</td>
                <td>{{ $role->description }}</td>
                @if($role->service_status_code == 'AC001')
                    <td><span class="badge rounded-pill bg-success text-light">Active</span></td>
                @else
                    <td><span class="badge rounded-pill bg-danger text-light">Not active</span></td>
                @endif
                <td>
                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_role_profile'))
                        <a href="{{ url('role', [$role->display_name, \Illuminate\Support\Facades\Crypt::encrypt($role->id)]) }}">
                            <button type="button" class="btn btn-primary btn-sm px-3"
                                    data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">
                                <i class="fas fa-eye"></i>
                            </button>
                        </a>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('edit_role'))
                        <button type="button" class="btn btn-warning btn-sm px-3" onclick="assignRole({{ $role->id }})"
                                data-mdb-toggle="modal" data-mdb-target="#editRoleModal1" data-mdb-placement="bottom"
                                title="Edit role" data-role-id="{{ $role->id }}" data-role-name="{{ $role->display_name }}"
                                data-role-description="{{ $role->description }}">
                            <i class="fas fa-pen"></i>
                        </button>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('block_role'))
                        <button type="button" class="btn btn-danger btn-sm px-3" data-mdb-placement="bottom"
                                data-mdb-toggle="modal" data-mdb-target="#roleBlockModal" data-row-id="{{ $role->id }}"
                                @if($role->service_status_code == 'AC001') title="Block role" data-service-action="Block {{ $role->display_name }}"
                                @else title="Unblock role"  data-service-action="Unblock {{ $role->display_name }}"@endif>
                            <i class="fas @if($role->service_status_code == 'AC001') fa-lock @else fa-unlock @endif"></i>
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Modal new role -->
<div class="modal fade left" id="roleModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
    aria-labelledby="roleModal1" aria-hidden="true">
    <div class="modal-dialog modal-side modal-top-left">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="roleModal1">Add a new role</h5>
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-mdb-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('role.create') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="form-outline mb-4">
                        <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                            is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                        <label class="form-label" for="typeRName">Role name *</label>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                    </div>
                    <div class="form-outline">
                        <input type="text" id="typeRDescription" class="form-control form-control-lg @error('description')
                            is-invalid @enderror" name="description" value="{{ old('description') }}" required/>
                        <label class="form-label" for="typeRDescription">Role description *</label>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('description') {{ $message }} @enderror</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-outline-primary" data-mdb-dismiss="modal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal edit role -->
<div class="modal fade left" id="editRoleModal1" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
    aria-labelledby="editRoleModal1" aria-hidden="true">
    <div class="modal-dialog modal-side modal-top-left">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="editRoleModal1">Edit Role</h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('role.edit') }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <input name="role_id" hidden/>
                    <div class="form-outline mb-4">
                        <input type="hidden" name="role_id" id="role_id" />
                        <input type="text" id="typeRName" class="form-control form-control-lg @error('name')
                            is-invalid @enderror" name="name" value="{{ old('name') }}" required/>
                        <label class="form-label" for="typeRName">Role name *</label>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                    </div>
                    <div class="form-outline mb-4">
                        <textarea class="form-control" id="typeRDescription" rows="4" name="description" required></textarea>
                        <label class="form-label" for="textAreaExample">role description *</label>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('description') {{ $message }} @enderror</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-outline-primary" data-mdb-dismiss="modal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal block/unblock role -->
<div class="modal fade left" id="roleBlockModal" data-mdb-backdrop="static" data-mdb-keyboard="false" tabindex="-1"
     aria-labelledby="roleBlockModal" aria-hidden="true">
    <div class="modal-dialog modal-side modal-top-left">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="roleBlockModal"></h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('role.block.toggle') }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    <input hidden type="text" name="row_id">
                    <div class="form-outline mb-4">
                        <textarea class="form-control" id="textAreaExample" rows="4" name="reason" required></textarea>
                        <label class="form-label" for="textAreaExample">Reason...</label>
                        <div class="invalid-feedback" style="white-space: nowrap;overflow: scroll">@error('name') {{ $message }} @enderror</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-outline-primary" data-mdb-dismiss="modal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var search_role_url = '{{ route('role.search') }}';

    function assignRole(role_id){
        $("#role_id").val(role_id);
    }
</script>

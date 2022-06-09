@extends('components.master')
@section('title')
Manage Permission & Roles
@endsection
@section('content')
@include('components.alert')

<div class="accordion" id="accordionExample">
    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_roles_list'))
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
                <strong> ROLES</strong>
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
            data-mdb-parent="#accordionExample">
          

                {{-- ROLES --}}
               
                @include('admin.roles_permissions.roles.index')
        
        </div>
    </div>
    @endif

    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_permission_list'))

    <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse"
                data-mdb-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                <strong>PERMISSIONS</strong>
            </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo"
            data-mdb-parent="#accordionExample">
         

                {{-- PERMISSIONS --}}
               
                @include('admin.roles_permissions.permissions.index')
               

          
        </div>
    </div>

    @endif

</div>



@endsection

@extends('components.master')
@section('title')
    Manage Permission & Roles
@endsection
@section('content')
    @include('components.alert')
{{--    ROLES --}}
    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_roles_list'))
        @include('admin.roles_permissions.roles.index')
    @endif

{{--    PERMISSIONS --}}
    @if(\Illuminate\Support\Facades\Auth::user()->hasPermission('view_permission_list'))
        @include('admin.roles_permissions.permissions.index')
    @endif
@endsection

@extends('layouts.app')


@section('content')



@if ($message = Session::get('success'))
<div class="alert alert-success">
  {{ $message }}
</div>
@endif


<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="row">
        <div class="col-lg-12 margin-tb">
          <div class="float-right my-3">
            @can('role-create')
            <a class="btn btn-success" href="{{ route('roles.create') }}"> Create New Role</a>
            @endcan
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <table class="table dt-responsive table-striped nowrap w-100">
            <thead>
              <tr>
                <th>No</th>
                <th>Name</th>
                <th width="280px">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($roles as $key => $role)
              <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $role->name }}</td>
                <td>
                  <a class="btn btn-info btn-sm" href="{{ route('roles.show',$role->id) }}">Show</a>
                  @can('role-edit')
                  <a class="btn btn-primary btn-sm" href="{{ route('roles.edit',$role->id) }}">Edit</a>
                  @endcan
                  @can('role-delete')
                  {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                  {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                  {!! Form::close() !!}
                  @endcan
                </td>
              </tr>
              @endforeach
            </tbody>

          </table>
          {{-- {!! $roles->render() !!} --}}
        </div>
      </div>
    </div>
  </div>
</div>





@endsection

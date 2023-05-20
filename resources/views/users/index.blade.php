@extends('layouts.app')

@section('additional-styles')
<style>
  button [type='submit'] {
    background-color: var(--bs-btn-bg) !important;
  }
</style>
@endsection

@section('content')

<div class="row mt-2">
    <div class="col-lg-12 margin-tb">
        <div class="pull-right mb-3">
            <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User</a>
        </div>
    </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  {{ $message }}
</div>
@endif


<table class="table table-bordered">
 <tr>
   <th>Username</th>
   <th>Name</th>
   <th>Phone #</th>
   <th>Roles</th>
   <th width="280px">Action</th>
 </tr>
 @foreach ($data as $key => $user)
  <tr>
    <td>{{ $user->username }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->phone_no }}</td>
    <td>
      @if(!empty($user->getRoleNames()))
        @foreach($user->getRoleNames() as $v)
           <label class="badge bg-success">{{ $v }}</label>
        @endforeach
      @endif
    </td>
    <td>
       <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}">Show</a>
       <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}">Edit</a>
        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
        {!! Form::close() !!}
    </td>
  </tr>
 @endforeach
</table>


{!! $data->render() !!}

@endsection
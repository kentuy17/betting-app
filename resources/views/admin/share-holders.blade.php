@extends('layouts.app')

@section('content')
<style>
  .supas {
    margin-left: auto;
  }
</style>
<div class="container mt-3">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="text-lg text-white">Share Allocations</h1>
            </div>
            <div class="col-sm-6">
              <ol class="supas breadcrumb float-sm-right float-right">
                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                <li class="breadcrumb-item active">Shares</li>
              </ol>
            </div>
          </div>
        </div>
      </section>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Share Holders</h3>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th style="width: 10px">%</th>
                <th>NAME</th>
                <th>ROLE</th>
                <th class="float-right" style="color:yellow !important;">COMMISSION</th>
                <th>POINTS</th>
                <th class="mr-2">ACTION</th>
              </tr>
            </thead>
            <tbody>
              @php $colors = [
                '4' => 'primary',
                '2' => 'danger',
                '1.5' => 'success',
                '0.5' => 'info'
              ]; @endphp
              @foreach ($share_holders as $key => $boss)
              <tr>
                <td class="text-center"><span class="badge bg-primary">{{ $boss->percentage }}%</span></td>
                <td>{{ $boss->user->name }}</td>
                <td>{{ $boss->role_description }}</td>
                <td class="text-right" style="color:yellow !important;">{{ number_format($boss->current_commission,2) }}</td>
                <td class="text-right">{{ number_format($boss->user->points,2) }}</td>
                <td class="mr-2">
                  <a href="javascript:void(0)" data-id="{{ $boss->id }}" class="btn btn-link text-info btn-icon btn-sm edit">
                    <i class="fa-solid fa-pen"></i></a>
                  <a href="javascript:void(0)" data-id="{{ $boss->id }}" class="btn btn-link text-danger btn-icon btn-sm delete mr-1">
                    <i class="fa-solid fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('additional-scripts')
  <script>
    $(function() {
      $('.edit').on('click', function() {
        let id = $(this).data('id')
        let percent = prompt('Enter new Percentage: ')
        if(percent > 10) {
          alert('Invalid value!!!');
        }
      })
    })
  </script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Detail Laporan')

@section('content')
<div class="p-4">
  @include('components.detail-data-sampah', ['data' => $data])
</div>
@endsection

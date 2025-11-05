@extends('layouts.app')

@section('title', 'Laporan KSM')

@section('content')
  {{-- Header KSM --}}
  @include('components.ksm_header')

  {{-- Form Input Data Sampah --}}
  @include('components.input_data_sampah')
@endsection

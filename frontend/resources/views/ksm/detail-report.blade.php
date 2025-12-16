@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')
<section class="p-0 min-h-screen bg-gray-50">
  @include('components.ksm_header')

  <div class="max-w-5xl mx-auto px-4 py-6">
    @include('components.detail-data-sampah', ['data' => $data])
  </div>
</section>

<script>
function closeDetail() {
  window.history.back();
}
</script>
@endsection
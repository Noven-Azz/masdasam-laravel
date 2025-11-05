
<div class="px-4 py-6 max-w-5xl mx-auto">
  <!-- Date card -->
  <div class="bg-green-700 rounded-lg shadow-md p-4 mb-6">
    <h3 class="text-white text-lg font-semibold mb-3">Tanggal Laporan</h3>
    <div class="bg-white rounded-md px-4 py-3 text-center text-gray-700 font-medium shadow-sm">
      {{ date('d-m-Y') }}
    </div>
  </div>

  <!-- Tabs (UI only) -->
  <nav class="flex flex-wrap justify-center gap-3 mb-6">
    <span class="inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm">Sampah Masuk</span>
    <span class="inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm">Hasil Pilahan</span>
    <span class="inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm">Pengangkutan</span>
    <span class="inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm">Pemusnahan</span>
    <span class="inline-block bg-white text-green-800 rounded-md px-4 py-2 text-sm shadow-sm">Timbunan</span>
  </nav>

  <form class="space-y-6" novalidate>
    @csrf

    <!-- Sampah Masuk card -->
    <section class="bg-green-700 rounded-lg shadow-md p-6">
      <h4 class="text-white text-2xl font-semibold mb-4">Sampah Masuk</h4>
      <div class="mb-4">
        <label class="block text-white mb-2">Total Sampah Masuk</label>
        <input type="text" name="sampah_masuk" inputmode="decimal" placeholder="Contoh: 1,25"
               class="w-full max-w-lg bg-white border border-gray-200 rounded-md px-4 py-3 text-gray-800 shadow-inner">
      </div>
    </section>

    <!-- Hasil Pilahan card -->
    <section class="bg-green-700 rounded-lg shadow-md p-6">
      <h4 class="text-white text-2xl font-semibold mb-4">Hasil Pilahan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Bursam</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Residu</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Rongsok</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
      </div>
    </section>

    <!-- Pengangkutan card -->
    <section class="bg-green-700 rounded-lg shadow-md p-6">
      <h4 class="text-white text-2xl font-semibold mb-4">Pengangkutan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Bursam</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Residu</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Rongsok</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
      </div>
    </section>

    <!-- Pemusnahan card -->
    <section class="bg-green-700 rounded-lg shadow-md p-6">
      <h4 class="text-white text-2xl font-semibold mb-4">Pemusnahan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Sampah Murni</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div class="md:col-span-2">
          <label class="block text-white mb-2">Residu</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
      </div>
    </section>

    <!-- Timbunan card -->
    <section class="bg-green-700 rounded-lg shadow-md p-6">
      <h4 class="text-white text-2xl font-semibold mb-4">Timbunan</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-white mb-2">Sampah Murni</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Bahan RDF</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Residu</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">RDF</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
        <div>
          <label class="block text-white mb-2">Rongsok</label>
          <input class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-gray-800" placeholder="Contoh: 1,25">
        </div>
      </div>
    </section>

    <!-- Submit -->
    <div class="flex justify-center">
      <button type="submit"
              class="bg-green-900 hover:bg-green-800 text-white font-semibold px-8 py-3 rounded-md w-full sm:w-80 transition-colors">
        Kirim
      </button>
    </div>
  </form>
</div>

@props(['roleName' => 'UPKP'])

<nav class="fixed top-0 left-0 right-0 z-[9999] flex justify-between items-center py-5 px-4 shadow-md bg-white border-b border-gray-200 backdrop-blur-sm md:px-6 md:py-7 lg:hidden">
  <div class="font-bold text-lg shadow-md bg-white py-1 px-3 md:text-2xl md:py-3 md:px-5">
    Admin {{ $roleName }}
  </div>
  <button onclick="toggleMobileMenu()" class="transition ease-in-out duration-300 lg:hidden" aria-label="Toggle Menu">
    <svg id="menuIcon" class="size-7 md:size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
    <svg id="closeIcon" class="size-7 md:size-10 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
  </button>
</nav>

<div id="mobileMenu" class="fixed top-[76px] left-0 w-full bg-white shadow-lg z-[9998] px-6 pt-4 transition-all ease-in-out duration-300 transform md:top-[102px] max-h-[calc(100vh-76px)] md:max-h-[calc(100vh-102px)] overflow-y-auto opacity-0 -translate-y-4 pointer-events-none">
  <div class="flex flex-col space-y-2">
    <a href="{{ $roleName === 'DLH' ? route('dlh.dashboard') : route('upkp.dashboard') }}"
       class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-700 hover:text-white transition">
      <svg class="size-6" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
      </svg>
      <span class="font-medium">Dashboard</span>
    </a>

    <a href="{{ $roleName === 'DLH' ? route('dlh.dashboard') : route('upkp.konfirmasi') }}"
       class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-700 hover:text-white transition">
      <svg class="size-6" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
      </svg>
      <span class="font-medium">Konfirmasi</span>
    </a>

    <a href="{{ $roleName === 'DLH' ? route('dlh.dashboard') : route('upkp.riwayat') }}"
       class="flex items-center gap-3 py-2 px-4 rounded-lg hover:bg-green-700 hover:text-white transition">
      <svg class="size-6" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
      </svg>
      <span class="font-medium">Riwayat</span>
    </a>
  </div>

  <div class="my-6">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="flex items-center gap-3 w-full py-2 px-4 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span class="font-medium">Logout</span>
      </button>
    </form>
  </div>
</div>

<aside class="hidden lg:flex lg:fixed lg:left-0 lg:top-0 lg:h-screen lg:w-64 xl:w-80 bg-white shadow-lg z-40 overflow-y-auto">
  <div class="flex flex-col items-center w-full">
    <div class="py-8 px-6 border-b-[3px] flex-shrink-0">
      <div class="font-bold text-xl shadow-md bg-white py-3 px-5">
        Admin {{ $roleName }}
      </div>
    </div>

    <div class="flex-1 py-6 px-6 overflow-y-auto w-full">
      <div class="flex flex-col space-y-2">
        <a href="{{ $roleName === 'DLH' ? route('dlh.dashboard') : route('upkp.dashboard') }}"
           class="flex items-center gap-3 py-3 px-4 rounded-lg hover:bg-green-700 hover:text-white transition">
          <svg class="size-7" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
          </svg>
          <span class="font-medium text-lg">Dashboard</span>
        </a>

        <a href="{{ $roleName === 'DLH' ? route('dlh.dashboard') : route('upkp.konfirmasi') }}"
           class="flex items-center gap-3 py-3 px-4 rounded-lg hover:bg-green-700 hover:text-white transition">
          <svg class="size-7" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          <span class="font-medium text-lg">Konfirmasi</span>
        </a>

        <a href="{{ $roleName === 'DLH' ? route('dlh.dashboard') : route('upkp.riwayat') }}"
           class="flex items-center gap-3 py-3 px-4 rounded-lg hover:bg-green-700 hover:text-white transition">
          <svg class="size-7" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
          </svg>
          <span class="font-medium text-lg">Riwayat</span>
        </a>
      </div>
    </div>

    <div class="py-10 px-6 border-t-[3px] flex-shrink-0 w-full">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
          <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span class="font-medium text-lg">Logout</span>
        </button>
      </form>
    </div>
  </div>
</aside>

@push('scripts')
<script>
function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  const menuIcon = document.getElementById('menuIcon');
  const closeIcon = document.getElementById('closeIcon');
  menu.classList.toggle('opacity-0');
  menu.classList.toggle('-translate-y-4');
  menu.classList.toggle('pointer-events-none');
  menuIcon.classList.toggle('hidden');
  closeIcon.classList.toggle('hidden');
}
function toggleDropdown() {
  const el = document.getElementById('dropdownMenu');
  if (el) el.classList.toggle('hidden');
}

// Handle logout forms - redirect to GET /logout if CSRF fails
document.addEventListener('DOMContentLoaded', function() {
  const logoutForms = document.querySelectorAll('form[action*="logout"]');
  logoutForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      // If CSRF token is missing or form submission fails, redirect to GET logout
      const csrfToken = form.querySelector('input[name="_token"]');
      if (!csrfToken || !csrfToken.value) {
        e.preventDefault();
        window.location.href = '/logout';
      }
    });
  });
});
</script>
@endpush
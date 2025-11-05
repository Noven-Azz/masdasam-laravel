<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - MASDASAM</title>

  @php $viteDev = env('VITE_DEV_SERVER_URL', 'http://localhost:5173'); @endphp

  @if (app()->environment('local'))
    <script type="module" src="{{ rtrim($viteDev, '/') }}@@vite/client"></script>
    <script type="module" src="{{ rtrim($viteDev, '/') }}/resources/js/app.js"></script>
    <link rel="stylesheet" href="{{ rtrim($viteDev, '/') }}/resources/css/app.css">
  @else
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
  @endif
</head>
<body class="overflow-hidden">
  <section id="login-ksm" class="flex flex-col w-full h-screen overflow-hidden">
    {{-- MOBILE AND TABLET VIEW --}}
    <div
      id="top-login-ksm"
      class="w-full bg-contain bg-no-repeat bg-top lg:hidden"
      style="background-image: url('{{ asset('images/bg-login-ksm.png') }}')"
    >
      <div class="hidden md:flex md:justify-center items-center md:pt-6">
        <img src="{{ asset('images/logo-dlh-banyumas.png') }}" alt="logo-dlh" class="w-36 h-[90px]" />
        <img src="{{ asset('images/logo-masdasam-hitam.png') }}" alt="logo-masdasam" class="w-40 md:w-56" />
      </div>
      <div class="flex flex-col items-start p-6 mt-[25%] md:mt-[10%] md:ms-10">
        <h1 class="text-3xl font-bold md:text-4xl">Login</h1>
        <p class="text-gray-800 mt-2 text-lg md:text-2xl">
          Silakan masukkan Email dan Password Anda untuk masuk.
        </p>
      </div>
    </div>

    <div
      id="bottom-login-mobile"
      class="w-full h-full bg-[#017F57] rounded-tl-[130px] md:rounded-t-[200px] lg:hidden"
    >
      <form
        id="mobileForm"
        action="{{ route('login') }}"
        method="POST"
        class="flex flex-col items-center justify-center gap-y-6 mt-[15%]"
      >
        @csrf

        {{-- Email --}}
        <div class="w-[70%] md:w-[60%]">
          <label for="email-mobile" class="block text-white text-lg mb-2">Email :</label>
          <input
            type="email"
            id="email-mobile"
            name="email"
            value="{{ old('email') }}"
            placeholder="Masukkan Email"
            required
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
          />
          @error('email')
            <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password --}}
        <div class="w-[70%] md:w-[60%]">
          <label for="password-mobile" class="block text-white text-lg mb-2">Password :</label>
          <div class="relative">
            <input
              type="password"
              id="password-mobile"
              name="password"
              placeholder="Masukkan Password"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
            />
            <button
              type="button"
              onclick="togglePassword('password-mobile', 'eyeIconMobile')"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
            >
              <svg id="eyeIconMobile" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
          @error('password')
            <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <button
          type="submit"
          class="w-[70%] py-4 rounded-xl bg-[#014D37] text-white text-xl font-semibold md:w-[60%] hover:bg-green-900 transition-colors"
        >
          Masuk
        </button>
      </form>

      <div class="flex justify-center mt-2">
        <a
          href="{{ route('password.request') }}"
          class="text-lg text-center text-white tracking-wide underline hover:text-gray-200 transition-colors"
        >
          Lupa Password ?
        </a>
      </div>
    </div>
    {{-- END MOBILE AND TABLET VIEW --}}

    {{-- DESKTOP VIEW --}}
    <div
      id="login-desktop"
      class="bg-[#014D37] hidden size-full bg-cover bg-no-repeat bg-bottom lg:block"
    >
      <div class="flex justify-start items-start">
        <div class="flex flex-col justify-between w-[40%]">
          <div class="flex items-center pt-4 gap-x-4 ps-10">
            <img
              src="{{ asset('images/logo-dlh-banyumas.png') }}"
              alt="logo-dlh"
              class="w-[105px] h-[65px] bg-white"
            />
            <img
              src="{{ asset('images/logo-masdasam-putih.png') }}"
              alt="logo-masdasam"
              class="w-40 h-14"
            />
          </div>
          <div class="flex flex-col items-start mx-auto mt-32">
            <h2 class="text-white text-6xl font-bold">REDUCE</h2>
            <h1 class="text-white text-8xl font-bold">RECYCLE</h1>
            <h2 class="text-white text-6xl font-bold">REUSE</h2>
          </div>
          <img
            src="{{ asset('images/img-recycle.png') }}"
            alt="Recycle Icon"
            class="absolute bottom-0 left-0 w-[240px]"
          />
        </div>

        <div class="flex flex-col w-[60%]">
          <div
            id="bottom-login-desktop"
            class="w-full min-h-[100vh] bg-[#017F57] rounded-s-[50px] overflow-hidden ms-0"
          >
            <div class="flex flex-col items-start p-6 ms-20 md:mt-[5%]">
              <h1 class="text-4xl font-bold text-white">Login</h1>
              <p class="mt-2 text-lg text-white">
                Silakan masukkan Email dan Password Anda untuk masuk.
              </p>
            </div>

            <form
              id="desktopForm"
              action="{{ route('login') }}"
              method="POST"
              class="flex flex-col items-center justify-center gap-y-8 mt-[6%]"
            >
              @csrf

              {{-- Email --}}
              <div class="w-[61%]">
                <label for="email-desktop" class="block text-white text-lg mb-2">Email :</label>
                <input
                  type="email"
                  id="email-desktop"
                  name="email"
                  value="{{ old('email') }}"
                  placeholder="Masukkan Email"
                  required
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                />
                @error('email')
                  <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- Password --}}
              <div class="w-[61%]">
                <label for="password-desktop" class="block text-white text-lg mb-2">Password :</label>
                <div class="relative">
                  <input
                    type="password"
                    id="password-desktop"
                    name="password"
                    placeholder="Masukkan Password"
                    required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
                  />
                  <button
                    type="button"
                    onclick="togglePassword('password-desktop', 'eyeIconDesktop')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
                  >
                    <svg id="eyeIconDesktop" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </div>
                @error('password')
                  <p class="text-red-200 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>

              <button
                type="submit"
                class="w-[50%] py-4 rounded-xl bg-[#014D37] text-white text-xl font-semibold lg:w-[61%] hover:bg-green-900 transition-colors"
              >
                Masuk
              </button>
            </form>

            <div class="flex justify-center mt-2">
              <a
                href="{{ route('password.request') }}"
                class="text-lg text-center text-white tracking-wide underline hover:text-gray-200 transition-colors"
              >
                Lupa Password ?
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
  function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
      input.type = 'text';
      icon.innerHTML = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
      `;
    } else {
      input.type = 'password';
      icon.innerHTML = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
      `;
    }
  }

  @if(session('success'))
    alert("{{ session('success') }}");
  @endif

  @if(session('error'))
    alert("{{ session('error') }}");
  @endif
  </script>
</body>
</html>
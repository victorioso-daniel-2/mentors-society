<template>
  <div class="min-h-screen w-full flex font-montserrat">
    <!-- Left: Form -->
    <div class="w-full md:w-2/5 flex flex-col justify-start items-center bg-[#f8f6f4] min-h-screen pt-16">
      <div class="w-full flex flex-col items-center justify-start" style="min-height: 60vh;">
        <img src="/resources/assets/ms_logo.png" alt="MS Logo" class="w-32 h-32 mb-8 mt-2" />
        <h2 class="text-4xl font-extrabold text-gray-800 mb-10 tracking-widest uppercase text-center" style="letter-spacing: 0.08em;">WELCOME!</h2>
        <form class="w-full flex flex-col gap-7 max-w-lg mx-auto items-center">
          <input
            type="text"
            placeholder="student number"
            class="px-7 py-5 rounded-lg border-none bg-[#463a2f] text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 font-montserrat text-lg w-full shadow-md transition-all duration-150"
          />
          <input
            type="password"
            placeholder="password"
            class="px-7 py-5 rounded-lg border-none bg-[#463a2f] text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 font-montserrat text-lg w-full shadow-md transition-all duration-150"
          />
          <button
            type="submit"
            class="w-64 py-5 rounded-lg bg-[#ffd600] hover:bg-yellow-400 text-gray-900 font-bold text-xl transition-colors duration-200 shadow-lg font-montserrat mx-auto"
          >
            Sign in
          </button>
        </form>
        <a href="#" class="mt-6 text-lg text-red-500 hover:underline font-semibold font-montserrat text-center block">Forgot Password</a>
      </div>
    </div>

    <!-- Right: Image -->
    <div class="hidden md:block w-3/5 h-screen relative">
      <img
        src="/resources/assets/another-bg.png"
        alt="Login Background"
        class="absolute inset-0 w-full h-full object-cover"
        draggable="false"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
};
</script>

<style scoped>
</style> 
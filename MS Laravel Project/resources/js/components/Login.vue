<template>
  <div class="min-h-screen w-full flex font-montserrat">
    <!-- Left: Form -->
    <div class="w-full md:w-2/5 flex flex-col justify-start items-center bg-[#f8f6f4] min-h-screen pt-16">
      <div class="w-full flex flex-col items-center justify-start" style="min-height: 60vh;">
        <img src="/resources/assets/ms_logo.png" alt="MS Logo" class="w-32 h-32 mb-8 mt-2" />
        <h2 class="text-4xl font-extrabold text-gray-800 mb-10 tracking-widest uppercase text-center" style="letter-spacing: 0.08em;">WELCOME!</h2>
        
        <!-- Error Message -->
        <div v-if="errorMessage" class="w-full max-w-lg mx-auto mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
          {{ errorMessage }}
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="w-full max-w-lg mx-auto mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
          {{ successMessage }}
        </div>

        <form @submit.prevent="handleLogin" class="w-full flex flex-col gap-7 max-w-lg mx-auto items-center">
          <input
            v-model="form.student_number"
            type="text"
            placeholder="student number"
            :disabled="isLoading"
            class="px-7 py-5 rounded-lg border-none bg-[#463a2f] text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 font-montserrat text-lg w-full shadow-md transition-all duration-150"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading }"
          />
          <input
            v-model="form.password"
            type="password"
            placeholder="password"
            :disabled="isLoading"
            class="px-7 py-5 rounded-lg border-none bg-[#463a2f] text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 font-montserrat text-lg w-full shadow-md transition-all duration-150"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading }"
          />
          <button
            type="submit"
            :disabled="isLoading"
            class="w-64 py-5 rounded-lg bg-[#ffd600] hover:bg-yellow-400 text-gray-900 font-bold text-xl transition-colors duration-200 shadow-lg font-montserrat mx-auto disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isLoading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Signing in...
            </span>
            <span v-else>Sign in</span>
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
import apiService from '../services/api.js';

export default {
  name: 'Login',
  data() {
    return {
      form: {
        student_number: '',
        password: ''
      },
      isLoading: false,
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async handleLogin() {
      // Clear previous messages
      this.errorMessage = '';
      this.successMessage = '';

      // Validate form
      if (!this.form.student_number.trim()) {
        this.errorMessage = 'Please enter your student number';
        return;
      }

      if (!this.form.password.trim()) {
        this.errorMessage = 'Please enter your password';
        return;
      }

      this.isLoading = true;

      try {
        const response = await apiService.login({
          student_number: this.form.student_number.trim(),
          password: this.form.password
        });

        if (response.success) {
          // Store the token
          apiService.setToken(response.data.token);
          
          // Store user data
          localStorage.setItem('user_data', JSON.stringify(response.data.user));
          localStorage.setItem('user_roles', JSON.stringify(response.data.roles));

          this.successMessage = 'Login successful! Redirecting...';
          
          // Redirect to student dashboard instantly
          window.location.href = '/student-dashboard';
        } else {
          this.errorMessage = response.message || 'Login failed';
        }
      } catch (error) {
        console.error('Login error:', error);
        
        // Handle different types of errors
        if (error.message.includes('Student number not found')) {
          this.errorMessage = 'Student number not found';
        } else if (error.message.includes('Invalid password')) {
          this.errorMessage = 'Invalid password';
        } else if (error.message.includes('User account not found')) {
          this.errorMessage = 'User account not found';
        } else if (error.message.includes('Password not set')) {
          this.errorMessage = 'Password not set for this account';
        } else if (error.message.includes('Validation failed')) {
          this.errorMessage = 'Please check your input and try again';
        } else {
          this.errorMessage = 'Login failed. Please try again.';
        }
      } finally {
        this.isLoading = false;
      }
    },

    clearMessages() {
      this.errorMessage = '';
      this.successMessage = '';
    }
  },
  mounted() {
    // Clear any existing auth data on component mount
    // This ensures fresh login state
    apiService.removeToken();
    localStorage.removeItem('user_data');
    localStorage.removeItem('user_roles');
  }
};
</script>

<style scoped>
/* Add any additional styles here */
</style> 
<template>
  <div class="login-page">
    <div class="login-box">
      <h1>ATM</h1>
      <p>Silakan masuk ke akun Anda</p>

      <form @submit.prevent="login">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          required
        />

        <input
          v-model="password"
          type="password"
          placeholder="PIN / Password"
          required
        />

        <button type="submit" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Login' }}
        </button>
      </form>

      <p v-if="error" class="error">
        {{ error }}
      </p>

      <p class="register">
        Belum punya akun?
        <router-link to="/register">Daftar</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      email: '',
      password: '',
      loading: false,
      error: ''
    }
  },

  methods: {
    async login() {
      this.loading = true
      this.error = ''

      try {
        const response = await axios.post(
          'http://127.0.0.1:8000/api/login',
          {
            email: this.email,
            password: this.password
          }
        )

        localStorage.setItem('token', response.data.token)

        localStorage.setItem(
          'user',
          JSON.stringify(response.data.user)
        )

        localStorage.setItem(
          'rekening',
          JSON.stringify(response.data.rekening)
        )

        this.$router.push('/dashboard')

      } catch (error) {
        if (error.response) {
          this.error =
            error.response.data.message ||
            'Email atau password salah'
        } else {
          this.error = 'Tidak dapat terhubung ke server'
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f3f4f6;
}

.login-box {
  width: 350px;
  padding: 30px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  margin-bottom: 5px;
}

p {
  text-align: center;
}

form {
  margin-top: 25px;
}

input {
  width: 100%;
  box-sizing: border-box;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

button {
  width: 100%;
  padding: 12px;
  border: none;
  border-radius: 6px;
  background: #2563eb;
  color: white;
  cursor: pointer;
}

button:disabled {
  opacity: 0.6;
}

.error {
  margin-top: 15px;
  color: red;
}

.register {
  margin-top: 20px;
}
</style>
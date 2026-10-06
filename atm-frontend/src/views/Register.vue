<template>
  <div class="register-page">
    <div class="register-box">
      <h1>ATM</h1>
      <p>Buat akun baru</p>

      <form @submit.prevent="register">
        <input
          v-model="name"
          type="text"
          placeholder="Nama"
          required
        />

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
          {{ loading ? 'Mendaftarkan...' : 'Daftar' }}
        </button>
      </form>

      <p v-if="success" class="success">
        {{ success }}
      </p>

      <p v-if="error" class="error">
        {{ error }}
      </p>

      <p class="login">
        Sudah punya akun?
        <router-link to="/login">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      loading: false,
      success: '',
      error: ''
    }
  },

  methods: {
    async register() {
      this.loading = true
      this.success = ''
      this.error = ''

      try {
        const response = await axios.post(
          'http://127.0.0.1:8000/api/register',
          {
            name: this.name,
            email: this.email,
            password: this.password
          }
        )

        this.success = response.data.message

        this.name = ''
        this.email = ''
        this.password = ''

        setTimeout(() => {
          this.$router.push('/login')
        }, 1500)

      } catch (error) {
        if (error.response) {
          this.error =
            error.response.data.message ||
            'Registrasi gagal'
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
.register-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f3f4f6;
}

.register-box {
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

.success {
  margin-top: 15px;
  color: green;
}

.login {
  margin-top: 20px;
}
</style>
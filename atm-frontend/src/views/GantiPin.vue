<template>
  <div class="page">
    <div class="box">
      <h1>Ganti PIN</h1>
      <p>Masukkan PIN lama dan PIN baru</p>

      <form @submit.prevent="gantiPin">

        <input
          v-model="pinLama"
          type="password"
          inputmode="numeric"
          maxlength="6"
          placeholder="PIN Lama"
          required
        />

        <input
          v-model="pinBaru"
          type="password"
          inputmode="numeric"
          maxlength="6"
          placeholder="PIN Baru (6 digit)"
          required
        />

        <button type="submit" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Ganti PIN' }}
        </button>

      </form>

      <p v-if="success" class="success">
        {{ success }}
      </p>

      <p v-if="error" class="error">
        {{ error }}
      </p>

      <button
        class="back"
        @click="$router.push('/dashboard')"
      >
        Kembali ke Dashboard
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      pinLama: '',
      pinBaru: '',
      loading: false,
      success: '',
      error: ''
    }
  },

  methods: {
    async gantiPin() {
      this.loading = true
      this.success = ''
      this.error = ''

      if (!/^\d{6}$/.test(this.pinBaru)) {
        this.error = 'PIN baru harus terdiri dari 6 digit angka'
        this.loading = false
        return
      }

      try {
        const token = localStorage.getItem('token')

        const response = await axios.put(
          'http://127.0.0.1:8000/api/ganti-pin',
          {
            pin_lama: this.pinLama,
            pin_baru: this.pinBaru
          },
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

        this.success = response.data.message

        this.pinLama = ''
        this.pinBaru = ''

        setTimeout(() => {
          localStorage.clear()
          this.$router.push('/login')
        }, 1500)

      } catch (error) {
        if (error.response?.status === 401) {
          this.error =
            error.response.data.message ||
            'PIN lama salah'
        } else if (error.response?.status === 422) {
          this.error =
            error.response.data.message ||
            'Data PIN tidak valid'
        } else {
          this.error =
            error.response?.data?.message ||
            'Gagal mengganti PIN'
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f3f4f6;
}

.box {
  width: 350px;
  padding: 30px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

h1 {
  text-align: center;
  margin-bottom: 5px;
}

p {
  text-align: center;
  color: #666;
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
  border-radius: 7px;
  background: #2563eb;
  color: white;
  cursor: pointer;
}

button:disabled {
  opacity: 0.6;
}

.success {
  margin-top: 15px;
  color: green;
}

.error {
  margin-top: 15px;
  color: red;
}

.back {
  margin-top: 15px;
  background: #6b7280;
}
</style>
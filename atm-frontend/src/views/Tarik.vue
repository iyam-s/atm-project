<template>
  <div class="page">
    <div class="container">

      <!-- HEADER -->
      <div class="header">
        <div>
          <span class="label">Transaksi</span>
          <h1>Tarik Tunai</h1>
        </div>

        <button
          class="back-btn"
          @click="$router.push('/dashboard')"
        >
          ← Dashboard
        </button>
      </div>

      <!-- FORM CARD -->
      <div class="card">

        <div class="icon">
          💸
        </div>

        <h2>Tarik Uang</h2>

        <p class="description">
          Masukkan jumlah uang yang ingin ditarik dari rekening Anda.
        </p>

        <!-- REKENING -->
        <div class="account-box">
          <span>Rekening</span>
          <strong>{{ rekening.nomor_rekening }}</strong>
        </div>

        <!-- FORM -->
        <form @submit.prevent="tarik">

          <label>Nominal Penarikan</label>

          <div class="input-wrapper">
            <span>Rp</span>

            <input
              v-model="nominal"
              type="number"
              min="10000"
              placeholder="Masukkan nominal"
              required
            />
          </div>

          <p class="hint">
            Minimal tarik tunai Rp 10.000
          </p>

          <button
            type="submit"
            class="submit-btn"
            :disabled="loading"
          >
            {{ loading ? 'Memproses...' : 'Tarik Tunai' }}
          </button>

        </form>

        <!-- MESSAGE -->
        <p v-if="success" class="success">
          {{ success }}
        </p>

        <p v-if="error" class="error">
          {{ error }}
        </p>

      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      rekening: JSON.parse(
        localStorage.getItem('rekening')
      ) || {},

      nominal: '',
      loading: false,
      success: '',
      error: ''
    }
  },

  methods: {
    async tarik() {
      this.loading = true
      this.success = ''
      this.error = ''

      try {
        const token = localStorage.getItem('token')

        const response = await axios.post(
          'http://127.0.0.1:8000/api/tarik',
          {
            nomor_rekening: this.rekening.nomor_rekening,
            nominal: Number(this.nominal)
          },
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

        this.success = response.data.message

        this.nominal = ''

        setTimeout(() => {
          this.$router.push('/dashboard?refresh=1')
        }, 1000)

      } catch (error) {
        console.log(error)

        if (error.response?.status === 401) {
          localStorage.clear()
          this.$router.push('/login')
          return
        }

        this.error =
          error.response?.data?.message ||
          'Gagal melakukan tarik tunai'

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
  background: #f3f4f6;
  padding: 30px 20px;
}

.container {
  max-width: 600px;
  margin: auto;
}

/* HEADER */

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.label {
  color: #6b7280;
  font-size: 14px;
}

.header h1 {
  margin: 4px 0 0;
  color: #111827;
  font-size: 28px;
}

.back-btn {
  border: none;
  background: #2563eb;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
}

.back-btn:hover {
  background: #1d4ed8;
}

/* CARD */

.card {
  background: white;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
  text-align: center;
}

.icon {
  width: 70px;
  height: 70px;
  margin: 0 auto 15px;

  border-radius: 50%;
  background: #fee2e2;

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 32px;
}

.card h2 {
  margin: 0;
  color: #111827;
}

.description {
  color: #6b7280;
  font-size: 14px;
  margin: 8px 0 20px;
}

/* ACCOUNT */

.account-box {
  background: #f9fafb;
  border-radius: 10px;
  padding: 14px;
  margin-bottom: 22px;
  text-align: left;
}

.account-box span {
  display: block;
  color: #6b7280;
  font-size: 12px;
  margin-bottom: 4px;
}

.account-box strong {
  color: #111827;
}

/* FORM */

form {
  text-align: left;
}

label {
  display: block;
  margin-bottom: 8px;
  color: #374151;
  font-size: 14px;
  font-weight: 500;
}

.input-wrapper {
  display: flex;
  align-items: center;

  border: 1px solid #d1d5db;
  border-radius: 10px;
  overflow: hidden;

  background: white;
}

.input-wrapper span {
  padding-left: 14px;
  color: #6b7280;
  font-weight: 500;
}

.input-wrapper input {
  width: 100%;
  border: none;
  outline: none;
  padding: 14px 12px;
  font-size: 16px;
}

.hint {
  margin: 7px 0 20px;
  color: #9ca3af;
  font-size: 12px;
}

/* BUTTON */

.submit-btn {
  width: 100%;
  border: none;
  background: #dc2626;
  color: white;

  padding: 14px;
  border-radius: 10px;

  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
}

.submit-btn:hover {
  background: #b91c1c;
}

.submit-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}

/* MESSAGE */

.success {
  margin-top: 18px;
  padding: 12px;

  background: #dcfce7;
  color: #15803d;

  border-radius: 8px;
  font-size: 14px;
}

.error {
  margin-top: 18px;
  padding: 12px;

  background: #fee2e2;
  color: #dc2626;

  border-radius: 8px;
  font-size: 14px;
}

/* MOBILE */

@media (max-width: 600px) {
  .page {
    padding: 20px 15px;
  }

  .header {
    align-items: flex-start;
    gap: 15px;
  }

  .header h1 {
    font-size: 23px;
  }

  .back-btn {
    padding: 8px 12px;
  }

  .card {
    padding: 25px 18px;
  }
}
</style>
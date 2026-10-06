<template>
  <div class="page">
    <div class="container">

      <!-- HEADER -->
      <div class="header">
        <div>
          <span class="label">Akun Saya</span>
          <h1>Profile</h1>
        </div>

        <button
          class="back-btn"
          @click="$router.push('/dashboard')"
        >
          ← Dashboard
        </button>
      </div>

      <!-- PROFILE CARD -->
      <div class="profile-card">

        <div class="avatar">
          👤
        </div>

        <h2>{{ user.name }}</h2>
        <p>{{ user.email }}</p>

      </div>

      <!-- DATA AKUN -->
      <div class="section">

        <h2>Informasi Akun</h2>

        <div class="info-list">

          <div class="info-item">
            <div class="icon blue">
              👤
            </div>

            <div>
              <span>Nama</span>
              <strong>{{ user.name }}</strong>
            </div>
          </div>

          <div class="info-item">
            <div class="icon purple">
              ✉️
            </div>

            <div>
              <span>Email</span>
              <strong>{{ user.email }}</strong>
            </div>
          </div>

          <div class="info-item">
            <div class="icon green">
              💳
            </div>

            <div>
              <span>Nomor Rekening</span>
              <strong>{{ rekening.nomor_rekening }}</strong>
            </div>
          </div>

          <div class="info-item">
            <div class="icon orange">
              💰
            </div>

            <div>
              <span>Saldo</span>
              <strong>Rp {{ formatSaldo(rekening.saldo) }}</strong>
            </div>
          </div>

        </div>

      </div>

      <!-- ACTION -->
      <div class="action">

        <button
          class="pin-btn"
          @click="$router.push('/ganti-pin')"
        >
          🔑 Ganti PIN
        </button>

      </div>

      <!-- ERROR -->
      <p v-if="error" class="error">
        {{ error }}
      </p>

    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      user: {},
      rekening: {},
      error: ''
    }
  },

  async mounted() {
    await this.getProfile()
  },

  methods: {
    async getProfile() {
      try {
        const token = localStorage.getItem('token')

        const response = await axios.get(
          'http://127.0.0.1:8000/api/profile',
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

        this.user = response.data.user
        this.rekening = response.data.rekening

      } catch (error) {
        console.log(error)

        if (error.response?.status === 401) {
          localStorage.clear()
          this.$router.push('/login')
        } else {
          this.error =
            error.response?.data?.message ||
            'Gagal mengambil data profile'
        }
      }
    },

    formatSaldo(value) {
      return Number(value || 0).toLocaleString('id-ID')
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
  max-width: 800px;
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
  font-size: 28px;
  color: #111827;
}

.back-btn {
  border: none;
  background: #2563eb;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.back-btn:hover {
  background: #1d4ed8;
}

/* PROFILE */

.profile-card {
  background: white;
  border-radius: 16px;
  padding: 30px;
  text-align: center;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
  margin-bottom: 20px;
}

.avatar {
  width: 75px;
  height: 75px;
  margin: auto;
  border-radius: 50%;
  background: #dbeafe;

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 35px;
}

.profile-card h2 {
  margin: 15px 0 5px;
  color: #111827;
}

.profile-card p {
  margin: 0;
  color: #6b7280;
}

/* SECTION */

.section {
  background: white;
  border-radius: 14px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.section h2 {
  margin: 0 0 20px;
  font-size: 19px;
  color: #111827;
}

/* INFO */

.info-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 15px;

  padding: 15px;
  border-radius: 10px;
  background: #f9fafb;
}

.info-item span {
  display: block;
  color: #6b7280;
  font-size: 13px;
  margin-bottom: 4px;
}

.info-item strong {
  color: #111827;
  font-size: 15px;
}

.icon {
  width: 43px;
  height: 43px;
  border-radius: 10px;

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 20px;
  flex-shrink: 0;
}

.blue {
  background: #dbeafe;
}

.purple {
  background: #ede9fe;
}

.green {
  background: #dcfce7;
}

.orange {
  background: #ffedd5;
}

/* ACTION */

.action {
  margin-top: 20px;
}

.pin-btn {
  width: 100%;
  border: none;
  background: #111827;
  color: white;

  padding: 14px;
  border-radius: 10px;

  cursor: pointer;
  font-size: 15px;
  font-weight: 500;
}

.pin-btn:hover {
  background: #1f2937;
}

/* ERROR */

.error {
  margin-top: 15px;
  padding: 12px;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 8px;
  text-align: center;
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

  .profile-card {
    padding: 25px 15px;
  }

  .section {
    padding: 20px 15px;
  }

  .info-item {
    padding: 13px;
  }
}
</style>
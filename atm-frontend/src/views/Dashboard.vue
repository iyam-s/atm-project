<template>
  <div class="dashboard">
    <div class="container">

      <!-- HEADER -->
      <div class="header">
        <div>
          <span class="label">Selamat datang</span>
          <h1>{{ user.name }}</h1>
        </div>

        <button class="logout-btn" @click="logout">
          Logout
        </button>
      </div>

      <!-- SALDO -->
      <div class="balance-card">
        <div class="balance-header">
          <span>Saldo Anda</span>
          <span class="wallet-icon">💳</span>
        </div>

        <h2>
          Rp {{ formatSaldo(saldo) }}
        </h2>

        <div class="account">
          <span>Nomor Rekening</span>
          <strong>{{ rekening.nomor_rekening }}</strong>
        </div>
      </div>

      <!-- MENU -->
      <h2 class="menu-title">Menu ATM</h2>

      <div class="menu">

        <button @click="go('/setor')" class="menu-card">
          <div class="menu-icon blue">
            💰
          </div>

          <div>
            <strong>Setor Tunai</strong>
            <span>Tambah saldo</span>
          </div>
        </button>

        <button @click="go('/tarik')" class="menu-card">
          <div class="menu-icon red">
            💸
          </div>

          <div>
            <strong>Tarik Tunai</strong>
            <span>Ambil uang</span>
          </div>
        </button>

        <button @click="go('/transfer')" class="menu-card">
          <div class="menu-icon purple">
            🔄
          </div>

          <div>
            <strong>Transfer</strong>
            <span>Kirim uang</span>
          </div>
        </button>

        <button @click="go('/riwayat')" class="menu-card">
          <div class="menu-icon orange">
            📜
          </div>

          <div>
            <strong>Riwayat</strong>
            <span>Lihat transaksi</span>
          </div>
        </button>

        <button @click="go('/profile')" class="menu-card">
          <div class="menu-icon green">
            👤
          </div>

          <div>
            <strong>Profile</strong>
            <span>Data akun</span>
          </div>
        </button>

        <button @click="go('/ganti-pin')" class="menu-card">
          <div class="menu-icon yellow">
            🔑
          </div>

          <div>
            <strong>Ganti PIN</strong>
            <span>Ubah PIN akun</span>
          </div>
        </button>

      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      user: JSON.parse(
        localStorage.getItem('user')
      ) || {},

      rekening: JSON.parse(
        localStorage.getItem('rekening')
      ) || {},

      saldo: 0
    }
  },

  async created() {
    await this.getSaldo()

    if (this.$route.query.refresh === '1') {
      this.$router.replace('/dashboard')
    }
  },

  methods: {
    async getSaldo() {
      try {
        const token = localStorage.getItem('token')

        const response = await axios.get(
          `http://127.0.0.1:8000/api/saldo/${this.rekening.nomor_rekening}`,
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

        this.saldo = response.data.saldo

      } catch (error) {
        console.log(error)

        if (error.response?.status === 401) {
          localStorage.clear()
          this.$router.push('/login')
        }
      }
    },

    formatSaldo(value) {
      return Number(value).toLocaleString('id-ID')
    },

    go(path) {
      this.$router.push(path)
    },

    async logout() {
      try {
        const token = localStorage.getItem('token')

        await axios.post(
          'http://127.0.0.1:8000/api/logout',
          {},
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

      } catch (error) {
        console.log(error)
      }

      localStorage.clear()
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f3f4f6;
  padding: 30px 20px;
}

.container {
  max-width: 900px;
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
  font-size: 26px;
  color: #111827;
}

.logout-btn {
  border: none;
  background: #dc2626;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.logout-btn:hover {
  background: #b91c1c;
}

/* SALDO */

.balance-card {
  background: #2563eb;
  color: white;
  padding: 28px;
  border-radius: 16px;
  box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
  margin-bottom: 30px;
}

.balance-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.balance-header span:first-child {
  font-size: 14px;
  opacity: 0.9;
}

.wallet-icon {
  font-size: 28px;
}

.balance-card h2 {
  font-size: 34px;
  margin: 12px 0 25px;
}

.account {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding-top: 15px;
  border-top: 1px solid rgba(255, 255, 255, 0.25);
}

.account span {
  font-size: 12px;
  opacity: 0.8;
}

.account strong {
  font-size: 16px;
}

/* MENU */

.menu-title {
  font-size: 20px;
  margin-bottom: 15px;
  color: #111827;
}

.menu {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 15px;
}

.menu-card {
  border: none;
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  text-align: left;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: 0.2s;
}

.menu-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.menu-card strong {
  display: block;
  color: #111827;
  margin-bottom: 5px;
}

.menu-card span {
  display: block;
  color: #6b7280;
  font-size: 13px;
}

.menu-icon {
  width: 45px;
  height: 45px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}

.blue {
  background: #dbeafe;
}

.red {
  background: #fee2e2;
}

.purple {
  background: #ede9fe;
}

.orange {
  background: #ffedd5;
}

.green {
  background: #dcfce7;
}

.yellow {
  background: #fef3c7;
}

/* RESPONSIVE */

@media (max-width: 700px) {
  .menu {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 500px) {
  .dashboard {
    padding: 20px 15px;
  }

  .header h1 {
    font-size: 22px;
  }

  .balance-card h2 {
    font-size: 28px;
  }

  .menu {
    grid-template-columns: 1fr;
  }

  .menu-card {
    padding: 16px;
  }
}
</style>
<template>
  <div class="page">
    <div class="container">

      <div class="header">
        <div>
          <h1>Riwayat Transaksi</h1>
          <p>Nomor Rekening: {{ rekening.nomor_rekening }}</p>
        </div>

        <button
          class="dashboard-btn"
          @click="$router.push('/dashboard')"
        >
          ← Dashboard
        </button>
      </div>

      <div class="saldo">
        <span>Saldo Sekarang</span>
        <h2>Rp {{ formatSaldo(saldo) }}</h2>
      </div>

      <div v-if="loading" class="message">
        Memuat riwayat transaksi...
      </div>

      <div v-else-if="error" class="message error">
        {{ error }}
      </div>

      <div v-else-if="transaksi.length === 0" class="empty">
        <div class="empty-icon">📜</div>
        <h3>Belum Ada Transaksi</h3>
        <p>Riwayat transaksi kamu akan muncul di sini.</p>
      </div>

      <div v-else class="list">

        <div
          v-for="item in transaksi"
          :key="item.id"
          class="transaction"
        >

          <div class="transaction-left">

            <div
              class="icon"
              :class="item.jenis === 'setor' ? 'masuk' : 'keluar'"
            >
              {{ item.jenis === 'setor' ? '↓' : '↑' }}
            </div>

            <div class="transaction-info">

              <strong>
                {{ getJenis(item) }}
              </strong>

              <small>
                {{ formatTanggal(item.created_at) }}
              </small>

              <small v-if="item.keterangan">
                {{ item.keterangan }}
              </small>

            </div>

          </div>

          <div
            class="nominal"
            :class="item.jenis === 'setor' ? 'masuk' : 'keluar'"
          >
            {{ getSign(item) }}
            Rp {{ formatSaldo(item.nominal) }}
          </div>

        </div>

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

      saldo: 0,
      transaksi: [],
      loading: true,
      error: ''
    }
  },

  async mounted() {
    await this.getRiwayat()
  },

  methods: {
    async getRiwayat() {
      try {
        const token = localStorage.getItem('token')

        const response = await axios.get(
          `http://127.0.0.1:8000/api/transaksi/${this.rekening.nomor_rekening}`,
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        )

        this.saldo = response.data.saldo_sekarang
        this.transaksi = response.data.transaksi

      } catch (error) {
        console.log(error)

        if (error.response?.status === 401) {
          localStorage.clear()
          this.$router.push('/login')
        } else {
          this.error =
            error.response?.data?.message ||
            'Gagal mengambil riwayat transaksi'
        }

      } finally {
        this.loading = false
      }
    },

    getJenis(item) {
      if (item.jenis === 'setor') {
        return 'Setor Tunai'
      }

      if (item.jenis === 'tarik') {
        return 'Tarik Tunai'
      }

      if (item.jenis === 'transfer') {
        return 'Transfer'
      }

      return item.jenis
    },

    getSign(item) {
      if (item.jenis === 'setor') {
        return '+'
      }

      return '-'
    },

    formatSaldo(value) {
      return Number(value).toLocaleString('id-ID')
    },

    formatTanggal(date) {
      return new Date(date).toLocaleString('id-ID')
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

.header h1 {
  margin: 0;
  font-size: 28px;
  color: #111827;
}

.header p {
  margin: 6px 0 0;
  color: #6b7280;
}

.dashboard-btn {
  border: none;
  background: #2563eb;
  color: white;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.dashboard-btn:hover {
  background: #1d4ed8;
}

/* SALDO */

.saldo {
  background: #2563eb;
  color: white;
  padding: 25px;
  border-radius: 14px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.saldo span {
  font-size: 14px;
  opacity: 0.9;
}

.saldo h2 {
  margin: 8px 0 0;
  font-size: 30px;
}

/* TRANSAKSI */

.list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.transaction {
  background: white;
  padding: 18px;
  border-radius: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.transaction-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.icon {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  font-weight: bold;
}

.icon.masuk {
  background: #dcfce7;
  color: #16a34a;
}

.icon.keluar {
  background: #fee2e2;
  color: #dc2626;
}

.transaction-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.transaction-info strong {
  color: #111827;
}

.transaction-info small {
  color: #6b7280;
}

.nominal {
  font-weight: bold;
  white-space: nowrap;
}

.masuk {
  color: #16a34a;
}

.keluar {
  color: #dc2626;
}

/* LOADING / ERROR */

.message {
  background: white;
  padding: 30px;
  border-radius: 12px;
  text-align: center;
  color: #6b7280;
}

.error {
  color: #dc2626;
}

/* KOSONG */

.empty {
  background: white;
  padding: 40px 20px;
  border-radius: 12px;
  text-align: center;
}

.empty-icon {
  font-size: 42px;
  margin-bottom: 10px;
}

.empty h3 {
  margin: 0 0 5px;
}

.empty p {
  color: #6b7280;
  margin: 0;
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
    font-size: 22px;
  }

  .dashboard-btn {
    padding: 8px 12px;
  }

  .transaction {
    padding: 14px;
  }

  .transaction-left {
    gap: 10px;
  }

  .icon {
    width: 38px;
    height: 38px;
    font-size: 18px;
  }

  .nominal {
    font-size: 14px;
  }
}
</style>
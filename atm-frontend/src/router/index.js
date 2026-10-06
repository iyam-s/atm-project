import { createRouter, createWebHistory } from 'vue-router'

import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import Setor from '../views/Setor.vue'
import Tarik from '../views/Tarik.vue'
import Transfer from '../views/Transfer.vue'
import Profile from '../views/Profile.vue'
import GantiPin from '../views/GantiPin.vue'
import Riwayat from '../views/Riwayat.vue'



const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/register',
    component: Register
  },

  {
    path: '/dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/setor',
    component: Setor,
    meta: { requiresAuth: true }
  },
  {
    path: '/tarik',
    component: Tarik,
    meta: { requiresAuth: true }
  },
  {
    path: '/transfer',
    component: Transfer,
    meta: { requiresAuth: true }
  },
  {
    path: '/riwayat',
    component: Riwayat,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/ganti-pin',
    component: GantiPin,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return '/login'
  }

  if ((to.path === '/login' || to.path === '/register') && token) {
    return '/dashboard'
  }

  return true
})


export default router


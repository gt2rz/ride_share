import { createRouter, createWebHistory } from 'vue-router'
// import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import LandingView from '../views/LandingView.vue'
import LocationView from '../views/LocationView.vue'
import MapView from '../views/MapView.vue'
import TripView from '../views/TripView.vue'
import StandbyView from '../views/StandbyView.vue'
import DriverView from '../views/DriverView.vue'
import DrivingView from '../views/DrivingView.vue'
import axios from 'axios'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView
    },
    {
      path: '/landing',
      name: 'landing',
      component: LandingView,
      meta: {
        requiresAuth: true
      }
    },
    {
      path: '/location',
      name: 'location',
      component: LocationView,
      meta: {
        requiresAuth: true
      }
    },
    {
      path: '/map',
      name: 'map',
      component: MapView,
      meta: {
        requiresAuth: true
      }
    },
    {
      path: '/trip',
      name: 'trip',
      component: TripView,
      meta: {
        requiresAuth: true
      }
    },
    {
      path: '/standby',
      name: 'standby',
      component: StandbyView,
      meta: {
        requiresAuth: true
      }
    },
    {
      path: '/driver',
      name: 'driver',
      component: DriverView,
      meta: {
        requiresAuth: true
      }
    },
    {
      path: '/driving',
      name: 'driving',
      component: DrivingView,
      meta: {
        requiresAuth: true
      }
    }
  ]
})

router.beforeEach((to, from, next) => {
  AuthVerify(to, from, next)
  next()
})

export default router

const AuthVerify = (to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!localStorage.getItem('token')) {
      return { name: 'login' }
    }

    axios.get('http://localhost/api/user', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    }).then(response => {
      if (response.status === 200) {
        localStorage.setItem('user', JSON.stringify(response.data))
        return next()
      }
    }).catch(error => {
      localStorage.removeItem('token')
      return { name: 'login' }
    })
  }
  return next()
}
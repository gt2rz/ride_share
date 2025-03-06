<template>
    <header>
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                           <strong>Ride Share App</strong>
                        </div>                        
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <div class="ml-3 relative">
                            <h1>DRIVER</h1>
                        </div> 
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">{{ title }}</h1>
        <div v-if="!trip.id" class="mt-8 flex justify-center">
            <Loader />
        </div>
        <div v-else>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapMap 
                            :zoom="14" 
                            :center="trip.destination" 
                            ref="gMap"
                            style="width:100%; height: 256px;"></GMapMap>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>{{ trip.destination_name }}</strong></p>
                    </div>
                </div>
                <div class="flex justify-between bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleDeclineTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">Decline</button>
                    <button
                        @click="handleAcceptTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">Accept</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import Loader from '@/components/Loader.vue'
import { onMounted, ref } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import {useLocationStore} from '../stores/location.js'
import {useTripStore} from '../stores/trip.js'
import http from '@/helpers/http.js'
import { useRouter } from 'vue-router'

const location = useLocationStore()
const trip = useTripStore()
const router = useRouter()

const title = ref('Waiting for ride request...')
const gMap = ref(null)

window.Pusher = Pusher;

onMounted(async () => {
    await location.updateCurrentLocation()

    let echo = new Echo({
        broadcaster: 'reverb',
        key: 'pnmszaaft9dqfsh2k01k',
        wsHost: 'localhost',
        wsPort: 8080,
        forceTLS: false,
        // disableStats: true,
        enabledTransports: ['ws', 'wss']
    })

    echo.connector.pusher.connection.bind('connected', function (socket) {
        console.info('Connected to ', socket)
    })

    echo.connector.pusher.connection.bind('error', function (error) {
        console.error('An error has occurred ', error)
    })

    echo.channel('drivers').listen('TripCreated', (message) => {
        title.value = 'Ride requested'
        trip.$patch(message.trip)
        console.log('TripCreated:', message);

        setTimeout(() => {
            initMapDirections()
        }, 2000)
    });
        
})

const handleDeclineTrip = () => {
    trip.reset()
    title.value = 'Waiting for ride request...'
}

const handleAcceptTrip = () => {
    http().post(`/api/trip/${trip.id}/accept`,{
        driver_location: location.current.geometry
    })
        .then((response) => {
            location.$patch({
                destination: {
                    name: 'Passenger',
                    geometry: response.data.origin,
                }
            })

            router.push({name: 'driving'})
        })
        .catch((err) => {
            console.error(err)
        })
}

const initMapDirections = () => {
    gMap.value.$mapPromise.then((mapObject) => {

        let originPoint = new google.maps.LatLng(trip.origin),
            destinationPoint = new google.maps.LatLng(trip.destination),
            directionsService = new google.maps.DirectionsService,
            directionsDisplay = new google.maps.DirectionsRenderer({
                map: mapObject
            })

        directionsService.route({
            origin: originPoint,
            destination: destinationPoint,
            avoidTolls: false,
            avoidHighways: false,
            travelMode: google.maps.TravelMode.DRIVING
        }, (res, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(res)
            } else {
                console.error(status)
            }
        })
    })
}
</script>
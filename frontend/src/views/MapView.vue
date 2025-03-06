<template>
    <div class="pt-16">
        <h1 class="text-3xl fonct-semibold mb-4">
            Here's a your trip
        </h1>
        <div class="overflow-hidden shadow sm:rounded max-w-sm mx-auto text-left">
            <div class="bg-white px-4 py-5 sm:p-6">
                <div>
                    <GMapMap 
                        :zoom="11" 
                        :center="location.destination.geometry" 
                        ref="gMap"
                        style="height: 256px; width: 100%;"
                    >
                        <!-- <GMapMarker :position="location.destination.geometry" /> -->
                    </GMapMap>
                </div>
                <div class="mt-2">
                    <p class="text-xl">
                        Goin to <strong> {{ location.destination.name }} </strong>
                    </p>
                </div>
            </div>
            <div clas="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                <button 
                    @click.prevent="handleConfirmTrip"
                    class="rounded-md border border-transparent bg-black px-4 py-2 my-2  text-white ">
                    Let's Go!
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useLocationStore } from '../stores/location.js';
import { useTripStore } from '@/stores/trip.js';
import { onMounted, ref } from 'vue';
import http from '@/helpers/http.js';

const location = useLocationStore()
const trip = useTripStore()
const router = useRouter()

const gMap = ref(null)

const handleConfirmTrip = () => {
    http().post('/api/trip',{
        origin: location.current.geometry,
        destination: location.destination.geometry,
        destination_name: location.destination.name

    },{
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        }
    
    })
    .then(response => {
        trip.$patch(response.data)
        router.push({ name: 'trip' })
    })
    .catch(error => {
        console.log(error)
    })
}

onMounted(async () => {
    // Does the user have a location set?
    if(location.destination.address == '') {
        router.push({ name: 'location' })
    }

    //Let get the ussers current location
    await location.updateCurrentLocation()

    //Draw a path on the map
    gMap.value.$mapPromise.then(mapObject => {

        let currentPoint = new google.maps.LatLng(location.current.geometry)
        let destinationPoint = new google.maps.LatLng(location.destination.geometry)

        let directionsService = new google.maps.DirectionsService();
        let directionsRenderer = new google.maps.DirectionsRenderer({
            map: mapObject
        });

        directionsService.route({
            origin: currentPoint,
            destination: destinationPoint,
            avoidTolls: true,
            avoidHighways: true,
            travelMode: google.maps.TravelMode.DRIVING
        }, (response, status) => {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsRenderer.setDirections(response);
            } else {
                console.log('Directions request failed due to ' + status);
            }
        });
    })

})

</script>

<style>

</style>
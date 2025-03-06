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
                            <h1>PASSENGER</h1>
                        </div> 
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">
            Where are we going?
        </h1>
        <form action="#">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <GMapAutocomplete
                            @place_changed="handleLocationChanged"
                            name="location" 
                            id="location" 
                            placeholder="Enter your destination"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm" />
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click.prevent="handleSelectLocation" 
                        type="button" 
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 text-white px-6">
                        Find a Ride
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useLocationStore } from '../stores/location.js';
import { useRouter } from 'vue-router';


const location = useLocationStore()
const router = useRouter()

const handleLocationChanged = (place) => {
    location.$patch({
        destination: {
            name: place.name,
            address: place.formatted_address,
            geometry: {
                lat: Number(place.geometry.location.lat()),
                lng: Number(place.geometry.location.lng())
            }
        }
    })

    console.log(location.destination);
}

const handleSelectLocation = () => {
    if(location.destination?.address == '') {
        alert('Please select a destination')
        return
    }

    router.push({ name: 'map' })
}
</script>

<style>
</style>
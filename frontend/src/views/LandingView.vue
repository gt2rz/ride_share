<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">
            Landing
        </h1>
        <div class="verflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-leff">
            <div class="bg-white px-4 py-5 sm:p-6">
                <div class="flex justify-between">
                    <button 
                        @click="handleStartDriving"
                        class="rounded-md border border-transparent bg-black py-2 px-4 font-medium text-white">
                        Start Driving
                    </button>
                    <button
                        @click="handleFindRide" 
                        class="rounded-md border border-transparent bg-black py-2 px-4 font-medium text-white">
                        Find a Ride
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import http from '@/helpers/http';
import { useRouter } from 'vue-router';

const router = useRouter()

const handleFindRide = () => {
    router.push({ name: 'location' })
}

const handleStartDriving = () => {

    http().get('/api/driver')
    .then(response => {
        if(response.data.driver){
            router.push({ name: 'standby' })
        }else{
            router.push({ name: 'driver' })
        }
    })
    .catch(error => {
        console.log(error)
    })
}

</script>

<style>

</style>
<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';




onMounted(() => {
    console.log('Index.vue mounted');


});




</script>


<template>
    <Head title="Home" />


    <FrontLayout>



        Welcome! <br/>

        <Link :href="route('election.create')" as="button">Create election</Link>




    </FrontLayout>
</template>

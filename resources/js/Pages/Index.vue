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



<style scoped>


.motions {
    display: grid;
    /* auto columns max with */

    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;

}

.motion {
    padding: 2rem;

    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    align-items: flex-start;

    gap: 10px;
}

button {
    padding: 1rem;
    background-color: beige;
}
</style>

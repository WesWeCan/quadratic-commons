<script setup lang="ts">


import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCircle } from '@mdi/js';

</script>



<template>
    <div class="remaining-credits">
        <div class="all-credits visualizer">

            <div class="credit" v-for="n in 100" :key="n" :title="n.toString()" >
                <svg-icon class="circle credit-bg" type="mdi" :path="mdiCircle" size="14" :data-creditCode="`d-${n}`"></svg-icon>
            </div>

        </div>
    </div>
</template>


<style lang="scss">




.all-credits.visualizer {

    width: max-content;

    display: grid;
    grid-template-columns: repeat(10, 1fr);
    grid-auto-rows: auto;

    gap: .1rem;


    margin-top: 5rem;
    padding: 2rem;


    transform: rotate(-45deg);

}


</style>

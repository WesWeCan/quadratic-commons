<script setup lang="ts">

import { onMounted, ref } from 'vue';

import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCircle } from '@mdi/js';


const props = defineProps({
    credits: {
        type: Number,
        required: true,
    },
})




const correctArray =
    [
        [1, 2, 5, 10, 17, 26, 37, 50, 65, 82],
        [4, 3, 6, 11, 18, 27, 38, 51, 66, 83],
        [9, 8, 7, 12, 19, 28, 39, 52, 67, 84],
        [16, 15, 14, 13, 20, 29, 40, 53, 68, 85],
        [25, 24, 23, 22, 21, 30, 41, 54, 69, 86],
        [36, 35, 34, 33, 32, 31, 42, 55, 70, 87],
        [49, 48, 47, 46, 45, 44, 43, 56, 71, 88],
        [64, 63, 62, 61, 60, 59, 58, 57, 72, 89],
        [81, 80, 79, 78, 77, 76, 75, 74, 73, 90],
        [100, 99, 98, 97, 96, 95, 94, 93, 92, 91]
    ]




onMounted(() => {


});



</script>


<template>
    <div class="remaining-credits">
        <div class="all-credits">
            <div class="credit background" v-for="n in props.credits" :key="n" :title="n.toString()"
                :data-creditCode="`bg-${n}`">
                <svg-icon class="circle credit-bg" type="mdi" :path="mdiCircle" :size="14"></svg-icon>
            </div>

            <!-- <div class="credit movable" v-for="n in props.credits" :key="n" :title="n.toString()"
                :data-creditCode="`c-${n}`" :data-targetCode="`bg-${n}`">
                <svg-icon class="circle credit-front" type="mdi" :path="mdiCircle" size="14"></svg-icon>
            </div> -->
        </div>
    </div>



</template>




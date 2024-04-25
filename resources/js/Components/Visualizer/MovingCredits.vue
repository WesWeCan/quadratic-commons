<script setup lang="ts">
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCircle } from '@mdi/js';

const props = defineProps({
    credits: {
        type: Number,
        required: true,
    },
})
</script>



<template>
    <div class="movable-credits">
        <div class="credit movable" v-for="n in props.credits" :key="n" :title="n.toString()"
                :data-creditCode="`c-${n}`" :data-targetCode="`bg-${n}`">
                <svg-icon class="circle credit-front" type="mdi" :path="mdiCircle" :size="12"></svg-icon>
            </div>
    </div>
</template>




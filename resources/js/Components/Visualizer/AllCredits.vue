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
    <div class="remaining-credits">
        <div class="all-credits">
            <div class="credit background" v-for="n in props.credits" :key="n" :title="n.toString()"
                :data-creditCode="`bg-${n}`">
                <svg-icon class="circle credit-bg" type="mdi" :path="mdiCircle" :size="12"></svg-icon>
            </div>
        </div>
    </div>
</template>




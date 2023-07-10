<script setup lang="ts">


import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCircle } from '@mdi/js';
import { computed } from 'vue';
import { onMounted, ref, watch } from 'vue';


const props = defineProps({
    code: {
        type: String,
        required: false,
        default: 'xxx'
    },

    opposed: {
        type: Boolean,
        default: false
    },


    credits: {
        type: Number,
        required: true,
    },

    forceSpread: {
        type: Boolean,
        required: false,
        default: false
    },


})


const referenceCredits = computed<number>(() => {

    if(props.forceSpread) {

        let temp = props.credits + 1;
        let tempSqrt = Math.sqrt(temp) - 1;
        temp = Math.pow(tempSqrt, 2);

        return temp;
    }

    return props.credits;

});

const correctArray = ref( [
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
    ]);


onMounted(() => {

    correctArray.value = generateTable(Math.sqrt(referenceCredits.value));

});


watch(() => props.credits, (newVal, oldVal) => {

    console.log('credits changed', newVal, oldVal, referenceCredits.value);
    correctArray.value = generateTable(Math.sqrt(referenceCredits.value));
});


function generateTable(votes: number) {

    let table = [];

    for (let i = 0; i < votes; i++) {
        table = addLayerToTable(table)
    }

    return table;

}


const addLayerToTable = (table: any[]) => {
    const tableCurrentRows = table.length;
    const tableCurrentColumns = tableCurrentRows;

    let currentValue = tableCurrentColumns * tableCurrentRows;

    table.push([]);
    for (let i = 0; i < tableCurrentColumns + 1; i++) {
        table[tableCurrentRows].push(0);
    }


    let row = 0;
    let col = 0;

    currentValue++;

    for (let i = 0; i < tableCurrentColumns; i++) {
        col++;
    }
    table[row][col] = currentValue;

    for (let i = 0; i < tableCurrentRows; i++) {
        table[row][col] = currentValue;
        currentValue++;
        row++;
    }

    for (let i = 0; i <= tableCurrentColumns; i++) {
        table[row][col] = currentValue;
        currentValue++;
        col--;
    }

    return table;

}


</script>



<template>

        <div class="all-credits visualizer" :class="[{'opposed': props.opposed}, `r-${Math.sqrt(referenceCredits)}`]">
            <div class="credit" v-for="n in correctArray.flat()" :key="n" :title="n.toString()">
                <svg-icon class="circle credit-bg" type="mdi" :path="mdiCircle" :size="14"
                    :data-creditCode="`d-${props.code}-${n}`"></svg-icon>
            </div>

        </div>

</template>


<script setup lang="ts">

import { onMounted, ref } from 'vue';

import SvgIcon from '@jamescoyle/vue-icon'
import { mdiCircle } from '@mdi/js';


const moveCredit = () => {
    console.log('moveCredit');


    // select the credit with data-creditCode c-25
    const creditToMove = document.querySelector('[data-creditCode="c-25"]');
    console.log(creditToMove);


    if (!creditToMove) {
        console.log('no credit to move');
        return;
    }

    // change the targetCode to d-5
    creditToMove.setAttribute('data-targetCode', `d-${Math.floor(Math.random() * 100) + 1}`);


    requestAnimationFrame(updateCredits);

}



const updateCredits = async () => {

    console.log('updateCredits');

    // get the current scroll position
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

    // get all movable credits
    const movableCredits = document.querySelectorAll('.movable');
    // console.log(movableCredits);

    // iterate over all movable credits
    movableCredits.forEach((credit, index) => {

        // get the targetcode
        const targetCode = credit.getAttribute('data-targetCode');
        // console.log(targetCode);

        // get the target position
        const targetPosition = document.querySelector(`[data-creditCode="${targetCode}"]`);
        // console.log(targetPosition);

        if (!targetPosition) {
            console.log('no target position');
            return;
        }

        // calculate the target position relative to the document
        const targetRect = targetPosition.getBoundingClientRect();
        let targetTop = targetRect.top + scrollTop;
        let targetLeft = targetRect.left + scrollLeft;


        if (targetCode && targetCode.startsWith('d')) {
            targetTop += 3;
            targetLeft += 3;
        }

        // animate the credit to the target position
        (credit as HTMLElement).style.top = `${targetTop}px`;
        (credit as HTMLElement).style.left = `${targetLeft}px`;

    });

    // requestAnimationFrame(updateCredits);

}



onMounted(async () => {

    requestAnimationFrame(updateCredits);

})


</script>



<template>
    <button @click="moveCredit">Move Credit</button>

    <div class="remaining-credits">
        <div class="all-credits">
            <div class="credit background" v-for="n in 100" :key="n" :title="n.toString()" :data-creditCode="`bg-${n}`">
                <svg-icon class="circle credit-bg" type="mdi" :path="mdiCircle" size="14"></svg-icon>
            </div>

            <div class="credit movable" v-for="n in 100" :key="n" :title="n.toString()" :data-creditCode="`c-${n}`"
                :data-targetCode="`bg-${n}`">
                <svg-icon class="circle credit-front" type="mdi" :path="mdiCircle" size="14"></svg-icon>
            </div>
        </div>
    </div>
</template>


<style lang="scss" >
.all-credits {

    width: max-content;

    display: grid;
    grid-template-columns: repeat(5, 1fr);
    grid-auto-rows: auto;

    gap: .1rem;



    .credit {
        position: relative;

        transition: all .1s ease-in-out;

        width: 14px;
        height: 14px;

        &.movable {
            top: 0;
            left: 0;
            position: absolute;
            z-index: 1;
        }

        .circle {
            position: absolute;
            top: 0;
            left: 0;
        }

        .credit-bg {
            color: lightgray;
        }

        .credit-front {
            color: black;
        }
    }
}
</style>

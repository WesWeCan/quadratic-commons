<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';
import VoteVisualizer from '@/Components/Visualizer/VoteVisualizer.vue';




import { mdiPlus, mdiMinus } from '@mdi/js';
import SvgIcon from '@jamescoyle/vue-icon';
import { computed } from 'vue';



const tempCredits = ref(10);
const maxIssues = 10;
const minIssues = 2;

const maxTempCredits = 10;
const minTempCredits = 1;

onMounted(() => {
    console.log('Index.vue mounted');

    for (let i = 0; i < minIssues; i++) {
        addIssue();
    }


});

const credits = computed<number>(() => {
    return Math.pow(form.motions.length, 2);
});

const form = useForm({
    name: "New Voting Round",
    description: "Description",
    credits: 100,
    motions: [] as VotingTypes.Motion[],
    options: {
        forceSpread: false,
    } as VotingTypes.ElectionOptions
});


/**
 * Submits the form and updates the 'form' object with the provided values.
 *
 * @param {None} None
 * @return {None} None
 */
const submit = () => {
    console.log('submit');
    // form.credits = credits.value;

    form.credits = Math.pow(tempCredits.value, 2);

    console.log(form);

    if(form.options.forceSpread) {
        form.credits -= 1;
    }


    form.post(route('election.store'));
};




const createUUID = () => {
    console.log('createUUID');

    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};


/**
 * Changes the credits based on the value of the `increases` parameter.
 *
 * @param {boolean} increases - Determines whether the credits should be increased or decreased.
 * @return {void}
 */
const changeCredits = (increases: boolean) => {

    if (tempCredits.value <= 1 && !increases) {
        return;
    }

    if (increases) {
        tempCredits.value++;
    } else {
        tempCredits.value--;
    }

    if (tempCredits.value > maxTempCredits) {
        tempCredits.value = maxTempCredits;
    }

    if (tempCredits.value < minTempCredits) {
        tempCredits.value = minTempCredits;
    }

    form.credits = Math.pow(tempCredits.value, 2);

};




/**
 * Adds an issue to the form's motions array if the maximum number of issues has not been reached.
 *
 * @param {void} - This function does not take any parameters.
 * @return {void} - This function does not return any value.
 */
const addIssue = () => {
    // Print a message to indicate that the function is being executed
    console.log('addIssue');

    // Check if the maximum number of issues has been reached
    if (form.motions.length >= maxIssues) {
        // If the maximum number of issues has been reached, return and exit the function
        return;
    }

    // Define a string of letters to be used for issue naming
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // Create a new motion object and add it to the motions array
    form.motions.push({
        content: `Issue ${letters[form.motions.length]}`,
        votes: 0,
        uuid: createUUID(),
        credits: 0,
    });
};

const removeIssue = (index: number) => {
    console.log('removeIssue');

    if (form.motions.length <= minIssues) {
        // Minimum number of issues reached
        return;
    }

    form.motions.splice(index, 1);
};

const moveIssue = (index: number, up: boolean) => {
    console.log('moveIssue');

    if (up) {
        form.motions.splice(index - 1, 0, form.motions.splice(index, 1)[0]);
    } else {
        form.motions.splice(index + 1, 0, form.motions.splice(index, 1)[0]);
    }

};



</script>


<template>
    <Head title="Create Voting Round" />

    <FrontLayout>

        <!-- <pre>
            {{ form.errors }}
        </pre>

        <pre>
            {{ form }}
        </pre> -->


        <h2>Create voting round</h2>

        <form @submit.prevent="submit">

            <label for="name">Name</label>
            <input type="text" id="name" v-model="form.name" />
            <div class="error" v-if="form.errors.name">{{ form.errors.name }}</div>

            <label for="description">Description</label>
            <textarea id="description" v-model="form.description"></textarea>
            <div class="error" v-if="form.errors.description">{{ form.errors.description }}</div>



            <label for="motions">Issues</label>
            <div class="error" v-if="form.errors.motions">{{ form.errors.motions }}</div>
            <div class="motions-form">

                <div class="motion" v-for="(motion, index) in form.motions" :key="index">
                    <label></label>
                    <input type="text" v-model="form.motions[index].content" />

                    <button @click.prevent="moveIssue(index, true)" :disabled="index === 0">Up</button>
                    <button @click.prevent="moveIssue(index, false)"
                        :disabled="index === form.motions.length - 1">Down</button>
                    <button @click.prevent="removeIssue(index)" :disabled="index < 1">Remove</button>

                </div>

                <button @click.prevent="addIssue()" :disabled="form.motions.length >= maxIssues">Add Issue</button>
            </div>




            <label for="credits">Spendable credits: {{ form.options.forceSpread ? form.credits-1 : form.credits }}</label>

            <div class="input-group">
                <input type="checkbox" v-model="form.options.forceSpread">
                <label>Choose to use {{form.credits-1}} credits</label>
            </div>


            <div class="credits-form">
                <input type="number" id="credits" :value="form.options.forceSpread ? form.credits-1 : form.credits" disabled />
                <button @click.prevent="changeCredits(true)" :disabled="tempCredits >= maxTempCredits"><svg-icon
                        class="circle credit-bg" type="mdi" :path="mdiPlus" :size="14"></svg-icon> Add credits</button>
                <button @click.prevent="changeCredits(false)" :disabled="tempCredits <= minTempCredits"><svg-icon
                        class="circle credit-bg" type="mdi" :path="mdiMinus" :size="14"></svg-icon> Remove credits</button>
                <div class="error" v-if="form.errors.credits">{{ form.errors.credits }}</div>

                <div class="credits-form-visualizer">
                    <VoteVisualizer :credits="form.options.forceSpread ? form.credits-1 : form.credits" :opposed="true" :force-spread="form.options.forceSpread" />
                </div>
            </div>





            <button type="submit">Create voting round</button>



        </form>




    </FrontLayout>
</template>



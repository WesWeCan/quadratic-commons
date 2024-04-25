<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';
import VoteVisualizer from '@/Components/Visualizer/VoteVisualizer.vue';
import { mdiPlus, mdiMinus } from '@mdi/js';
import SvgIcon from '@jamescoyle/vue-icon';


const tempCredits = ref(10);
const maxIssues = 10; // it is advised to keep the maximum number of issues to 10
const minIssues = 2;

const maxTempCredits = 10;
const minTempCredits = 1;

/**
 * Executes the code inside the onMounted callback function.
 * Adds an issue to the election for each iteration up to the minimum number of issues.
 */
onMounted(() => {
    for (let i = 0; i < minIssues; i++) {
        addIssue();
    }
});

/**
 * This code initializes a form object using the `useForm` function from the Inertia.js library.
 * The `useForm` function is used to create a reactive form object that can be used to manage form state.
 * 
 * The `form` object has the following properties:
 * - `name`: A string representing the name of the voting round.
 * - `description`: A string representing the description of the voting round.
 * - `credits`: A number representing the number of credits for the voting round.
 * - `motions`: An array of `VotingTypes.Motion` objects representing the motions for the voting round.
 * - `options`: An object of type `VotingTypes.ElectionOptions` representing the options for the voting round.
 *   - `forceSpread`: A boolean indicating whether to force the spread of votes.
 * 
 * This code is part of the ElectionCreate.vue component.
 * 
 * @see https://inertiajs.com/forms
 */

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
 * Submits the form data and posts it to the server.
 */
const submit = () => {
    form.credits = Math.pow(tempCredits.value, 2);

    if(form.options.forceSpread) {
        form.credits -= 1;
    }

    form.post(route('election.store'));
};


/**
 * Generates a random UUID.
 * 
 * @returns {string} The generated UUID.
 */
const createUUID = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};

/**
 * Function to change the credits value.
 * @param {boolean} increases - Indicates whether the credits should be increased or decreased.
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
 * Adds a new issue to the motions array.
 * 
 * @returns {void}
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

/**
 * Removes an issue from the form's motions array at the specified index.
 * 
 * @param {number} index - The index of the issue to be removed.
 * @returns {void}
 */
const removeIssue = (index: number) => {
    if (form.motions.length <= minIssues) {
        // Minimum number of issues reached
        return;
    }

    form.motions.splice(index, 1);
};

/**
 * Moves an issue in the form's motions array.
 * 
 * @param {number} index - The index of the issue to be moved.
 * @param {boolean} up - Indicates whether the issue should be moved up (false means move down).
 */
const moveIssue = (index: number, up: boolean) => {
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
        <h2>Create voting round</h2>

        <form @submit.prevent="submit">
            <label for="name">Name:</label>
            <input type="text" id="name" v-model="form.name" />
            <div class="error" v-if="form.errors.name">{{ form.errors.name }}</div>

            <label for="description">Description:</label>
            <textarea id="description" v-model="form.description"></textarea>
            <div class="error" v-if="form.errors.description">{{ form.errors.description }}</div>

            <label for="motions">Issues to vote on:</label>
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

            <label for="credits">Spendable credits: <u>{{ form.options.forceSpread ? form.credits-1 : form.credits }}</u></label>

            <div class="input-group">
                <input type="checkbox" v-model="form.options.forceSpread">
                <label>Choose to use {{form.credits-1}} credits to prevents the voter to put all credits on one vote.</label>
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



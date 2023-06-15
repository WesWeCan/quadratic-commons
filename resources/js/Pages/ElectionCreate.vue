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

onMounted(() => {
    console.log('Index.vue mounted');



   for(let i = 0; i < minIssues; i++) {
       addIssue();
   }


});




const credits = computed<number>(() => {
    return Math.pow(form.motions.length, 2);
});

const form = useForm({
    name: "New Voting Round",
    description: "Description",
    credits: -1,
    motions: [] as VotingTypes.Motion[],
    options: {}
});


const submit = () => {
    console.log('submit');
    form.credits = credits.value;
    form.post(route('election.store'));
};




const createUUID = () => {
    console.log('createUUID');

    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};


const changeCredits = (increases: boolean) => {

    if (tempCredits.value <= 1 && !increases) {
        return;
    }

    if (increases) {
        tempCredits.value++;
    } else {
        tempCredits.value--;
    }

    form.credits = Math.pow(tempCredits.value, 2);

};




const addIssue = () => {
    console.log('addIssue');

    if (form.motions.length >= maxIssues) {
        // Maximum number of issues reached
        return;
    }

    form.motions.push({
        content: `Issue ${form.motions.length + 1}`,
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

const moveIssue = (index: number, up : boolean) => {
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

                    <button
                        @click.prevent="moveIssue(index, true)" :disabled="index === 0">Up</button>
                    <button
                        @click.prevent="moveIssue(index, false)" :disabled="index === form.motions.length - 1">Down</button>
                    <button @click.prevent="removeIssue(index)" :disabled="index < 1">Remove</button>

                </div>

                <button @click.prevent="addIssue()" :disabled="form.motions.length >= maxIssues">Add Issue</button>
            </div>




            <label for="credits">Spendable credits {{ credits }}</label>
            <VoteVisualizer :credits="credits" :opposed="false" />


            <!-- <input type="number" id="credits" v-model="form.credits" disabled/>
            <button @click.prevent="changeCredits(true)"><svg-icon class="circle credit-bg" type="mdi" :path="mdiPlus" size="14"></svg-icon> </button>
            <button @click.prevent="changeCredits(false)"><svg-icon class="circle credit-bg" type="mdi" :path="mdiMinus" size="14"></svg-icon></button>
            <div class="error" v-if="form.errors.credits">{{ form.errors.credits }}</div> -->


            <button type="submit">Create voting round</button>



        </form>




    </FrontLayout>
</template>



<style scoped></style>

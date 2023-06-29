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
const minIssues = 1;

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
        votes: 2, // Set initial votes to 2
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
    <Head title="Created!" />

    <FrontLayout>





        <h1>"{{ $page.props.election.name }}" has been created</h1>



        <div>
        <p>Share this link with your commons:</p>
        <a :href="route('election.vote', $page.props.election.uuid)">{{ route('election.vote', $page.props.election.uuid) }}</a>
        <br/>
        <Link :href="route('election.vote', $page.props.election.uuid)" as="button">Go to {{ $page.props.election.name }}</Link>
        </div>
<br/><br/>

        <div>
        <p>Share this link to view the results:</p>
        <a :href="route('election.results', $page.props.election.uuid)">{{ route('election.results', $page.props.election.uuid) }}</a>
        <br/>
        <Link :href="route('election.results', $page.props.election.uuid)" as="button">See results</Link>
        </div>

    </FrontLayout>
</template>



<style scoped></style>

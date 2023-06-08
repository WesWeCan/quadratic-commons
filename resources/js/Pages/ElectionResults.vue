<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref, computed } from 'vue';
import { Link, Head, useForm, usePage } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';


const maxCredits = ref<number>(100);
const credits = ref<number>(100);

const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);


const calculateCost = (votes: number) => {

    let numberOfVotes = votes;

    let cost = Math.pow(numberOfVotes, 2);
    console.log(numberOfVotes, cost);

    return cost;
};





onMounted(() => {

});


const motionVotes = (elctionMotion: VotingTypes.Motion) => {

    let votes = 0;

    if (!Election.value.votes) {
        return votes;
    }

    Election.value.votes.forEach((vote) => {

        vote.motions.forEach((voteMotion) => {
            if (voteMotion.uuid === elctionMotion.uuid) {
                votes += voteMotion.votes;
            }
        });

    });

    return votes;
};


const motionCredits = (elctionMotion: VotingTypes.Motion) => {

    let credits = 0;

    if (!Election.value.votes) {
        return credits;
    }

    Election.value.votes.forEach((vote) => {

        vote.motions.forEach((voteMotion) => {
            if (voteMotion.uuid === elctionMotion.uuid) {
                credits += voteMotion.credits;
            }
        });

    });

    return credits;
};

</script>


<template>

    <Head :title="($page.props.election as VotingTypes.Election).name" />

    <FrontLayout>


<!-- ranking -->
<!-- percentages  -->


        <div class="election-results">


            <div class="election-results__header">
                <h1>{{ Election.name }}</h1>
                <p>{{ Election.description }}</p>
            </div>



            <div class="motions-results">

                <div class="motion-result" v-for="motion in Election.motions" :key="motion.uuid">

                    <h2>{{ motion.content }}</h2>

                    <div class="motion_votes">
                        <p>Number of votes: {{ motionVotes(motion) }}</p>
                        <p>Total Credits spend: {{ motionCredits(motion) }}</p>
                    </div>

                </div>

            </div>


        </div>


        <!-- <pre>
        {{ $page.props.election }}
        </pre> -->





    </FrontLayout>
</template>



<style scoped>
.election-results {
    padding: 3rem;
}

.motions-results {
    display: grid;
    /* auto columns max with */

    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;

    margin-top: 2rem;

}

.motion {
    padding: 2rem;

    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    align-items: flex-start;

    gap: 10px;
}

button {
    padding: 1rem;
    background-color: beige;
}
</style>

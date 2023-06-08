<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head, useForm, usePage } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';
import AllCredits from '@/Components/Visualizer/AllCredits.vue';
import VoteVisualizer from '@/Components/Visualizer/VoteVisualizer.vue';




const maxCredits = ref<number>(100);
const credits = ref<number>(100);

const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);
const Vote = ref<VotingTypes.Vote>({
    name: 'Annonymous',
    remainingCredits: JSON.parse(JSON.stringify(Election.value.credits)),
    motions: JSON.parse(JSON.stringify(Election.value.motions)),
    election_uuid: Election.value.uuid,
});




/**
 * Casts a vote for a motion using quadratic voting.
 *
 * Quadratic voting is a voting system in which voters can allocate a number of votes to a particular issue,
 * but the cost of each vote increases quadratically with the number of votes allocated.
 * This system allows voters to express the intensity of their preferences more accurately than traditional
 * voting systems, and can help to prevent the tyranny of the majority.
 *
 * @param {Motion} motion - The motion to cast a vote for.
 * @param {boolean} inFavor - Whether the vote is in favor or opposed.
 * @returns {void}
 */
const castVote = (motion: VotingTypes.Motion, inFavor: boolean) => {

    // Calculates the cost of a vote using quadratic voting
    const votesCast = motion.votes;
    const voteCurrentWorth = Math.pow(votesCast, 2);

    const voteNewWorth = Math.pow(votesCast + (inFavor ? 1 : -1), 2);
    const voteCost = voteNewWorth - voteCurrentWorth;

    // Checks if the user has enough credits to cast the vote
    if(Vote.value.remainingCredits - voteCost >= 0 && Vote.value.remainingCredits - voteCost <= Election.value.credits) {
        Vote.value.remainingCredits -= voteCost;
    }
    else {
        console.log('Not enough credits');
        return;
    }

    // Updates the vote count for the motion
    if(inFavor) {
        motion.votes++;
    } else {
        motion.votes--;
    }


    motion.credits = calculateCost(motion.votes);
}


const calculateCost = (votes: number) => {

    let numberOfVotes = votes;

    let cost = Math.pow(numberOfVotes, 2);
    console.log(numberOfVotes, cost);

    return cost;
};


const form = useForm(Vote.value);


onMounted(() => {
    console.log('Index.vue mounted');

    Vote.value = {
    name: 'Annonymous',
    remainingCredits: JSON.parse(JSON.stringify(Election.value.credits)),
    motions: JSON.parse(JSON.stringify(Election.value.motions)),
    election_uuid: Election.value.uuid,
}

});



const submitForm = () => {
    console.log('submitForm');

    form.election_uuid = Election.value.uuid;
    form.name = Vote.value.name;
    form.remainingCredits = Vote.value.remainingCredits;
    form.motions = Vote.value.motions;

    form.post(route('vote.store'))
}


</script>


<template>

    <Head :title="($page.props.election as VotingTypes.Election).name" />

    <FrontLayout>


<!--
        <pre>
        {{ $page.props.election }}
        </pre> -->

        <div>
            <h1>{{ Election.name }}</h1>
            <h2>Max Credits: {{ Election.credits }}</h2>

            <h2>Remaining Credits: {{ Vote.remainingCredits }}</h2>

            <label>Name: </label>
            <input type="text" v-model="Vote.name" />

            <div class="motions">

            <div class="motion" v-for="(motion, index) in Vote.motions" :key="index">


                <h2>{{ motion.content }}</h2>

                <span>{{ motion.votes }} votes</span>

                <span>Cost: {{ calculateCost(motion.votes) }}</span>


                <button @click="castVote(motion, true)">In favor</button>
                <button @click="castVote(motion, false)">Opposed</button>

                <br /><br />
            </div>
            </div>
        </div>




<!--
        <pre>
        {{ Vote }}
        </pre> -->

        <div class="results">
            <h1>My Vote</h1>

                <div class="motions-results">

                <div class="motion-result" v-for="(motion, index) in Vote.motions" :key="index">
                    {{ motion.content }} | {{ motion.votes }} votes {{ calculateCost(motion.votes) }} credits
                </div>
                </div>


                <form @submit.prevent="submitForm">

                    <!-- <pre>
                    {{ form.errors }}
                    </pre> -->

                    <button type="submit">Vote</button>
                </form>


        </div>



        <AllCredits></AllCredits>

        <VoteVisualizer></VoteVisualizer>

    </FrontLayout>
</template>



<style scoped>


.motions {
    display: grid;
    /* auto columns max with */

    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;

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

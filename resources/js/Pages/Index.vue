<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';

interface Motion {
    content: string;
    votes: number;
};


const maxCredits = ref<number>(100);
const credits = ref<number>(100);

const motions = ref<Motion[]>([]);

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
const castVote = (motion: Motion, inFavor: boolean) => {

    // Calculates the cost of a vote using quadratic voting
    const votesCast = motion.votes;
    const voteCurrentWorth = Math.pow(votesCast, 2);

    const voteNewWorth = Math.pow(votesCast + (inFavor ? 1 : -1), 2);
    const voteCost = voteNewWorth - voteCurrentWorth;

    // Checks if the user has enough credits to cast the vote
    if(credits.value - voteCost >= 0 && credits.value - voteCost <= maxCredits.value) {
        credits.value -= voteCost;
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

}


const calculateCost = (votes: number) => {

    let numberOfVotes = votes;

    let cost = Math.pow(numberOfVotes, 2);
    console.log(numberOfVotes, cost);

    return cost;
};



onMounted(() => {
    console.log('Index.vue mounted');

    for (let i = 1; i <= 10; i++) {
        motions.value.push({
            content: 'Motion ' + i,
            votes: 0,
        });
    }

    for (let i = 1; i <= 10; i++) {
        // castVote(motions.value[0], true);
    }

});




</script>


<template>
    <FrontLayout>



        <div>
            <h1>Available Credits: {{ credits }}</h1>


            <div class="motions">

            <div class="motion" v-for="(motion, index) in motions" :key="index">


                <h2>{{ motion.content }}</h2>

                <span>{{ motion.votes }} votes</span>


                <button @click="castVote(motion, true)">In favor</button>
                <button @click="castVote(motion, false)">Opposed</button>

                <br /><br />
            </div>
            </div>
        </div>



        <div class="results">
            <h1>Results</h1>

                <div class="motions-results">

                <div class="motion-result" v-for="(motion, index) in motions" :key="index">
                    {{ motion.content }} {{ motion.votes }}
                </div>
                </div>

        </div>





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

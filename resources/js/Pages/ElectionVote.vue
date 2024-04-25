<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head, useForm, usePage } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';
import AllCredits from '@/Components/Visualizer/AllCredits.vue';
import VoteVisualizer from '@/Components/Visualizer/VoteVisualizer.vue';
import MovingCredits from '@/Components/Visualizer/MovingCredits.vue';
import Tutorial from '@/Components/Tutorial.vue';
import { computed } from 'vue';

const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);
const Vote = ref<VotingTypes.Vote>({
    name: '',
    remainingCredits: JSON.parse(JSON.stringify(Election.value.credits)),
    motions: JSON.parse(JSON.stringify(Election.value.motions)),
    election_uuid: Election.value.uuid,
});

const form = useForm(Vote.value);
const visualCredits = ref([] as VotingTypes.Credit[]);


onMounted(() => {
    console.log('Index.vue mounted');

    Vote.value = {
        name: '',
        remainingCredits: JSON.parse(JSON.stringify(Election.value.credits)),
        motions: JSON.parse(JSON.stringify(Election.value.motions)),
        election_uuid: Election.value.uuid,
    }

    initCredits();

    requestAnimationFrame(updateCredits);

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
    if (Vote.value.remainingCredits - voteCost >= 0 && Vote.value.remainingCredits - voteCost <= Election.value.credits) {
        Vote.value.remainingCredits -= voteCost;
    }
    else {
        console.log('Not enough credits');
        return;
    }

    // Updates the vote count for the motion
    if (inFavor) {
        motion.votes++;

    } else {
        motion.votes--;
    }

    visualizeVote(inFavor, motion);
    motion.credits = calculateCost(motion.votes);
}


/**
 * Calculates the cost based on the number of votes.
 *
 * @param {number} votes - The number of votes.
 * @return {number} The calculated cost.
 */
const calculateCost = (votes: number) => {

    let numberOfVotes = votes;

    let cost = Math.pow(numberOfVotes, 2);
    // console.log(numberOfVotes, cost);

    return cost;
};


/**
 * Calculates the number of votes based on the given credits.
 * @param {number} credits - The number of credits.
 * @returns {number} - The calculated number of votes.
 */
const calculateVotesInterger = (credits: number) => {

    let numberOfVotes = 0;

    if (Election.value.options.forceSpread) {
        numberOfVotes = Math.sqrt(credits + 1) - 1;
    }
    else {
        numberOfVotes = Math.sqrt(credits);
    }

    return numberOfVotes;
};


/**
 * Resets the credits for each motion in the vote.
 * For each motion, it casts votes in the opposite direction of the current votes.
 */
const resetCredits = () => {
    Vote.value.motions.forEach((motion) => {

        let inFavor = motion.votes > 0;
        for (let i = Math.abs(motion.votes); i > 0; i--) {

            castVote(motion, !inFavor);

        }
    });
}


/**
 * Submits the form by sending a POST request to the server.
 *
 * @param {type} form - The form object containing the data to be submitted.
 * @return {type} The response from the server indicating the success of the submission.
 */
const submitForm = () => {
    console.log('submitForm');

    form.election_uuid = Election.value.uuid;
    form.name = Vote.value.name ? Vote.value.name : "Unknown";
    form.remainingCredits = Vote.value.remainingCredits;
    form.motions = Vote.value.motions;

    form.post(route('vote.store'))
}


/**
 * Initializes the visual credits array with the number of credits available for the election.
 * Each credit is represented by an object with a unique creditCode and targetCode.
 * The creditCode is used to identify the credit in the visual representation.
 * The targetCode is used to identify the target element in the DOM where the credit will be displayed.
 *
 * @return {void}
 */
const initCredits = () => {
    for (let i = 1; i <= Election.value.credits; i++) {
        visualCredits.value.push({
            creditCode: `c-${i}`,
            targetCode: `bg-${i}`,
        });
    }

}


/**
 * Visualizes the vote by updating the visual representation of the credits.
 *
 * @param {boolean} inFavor - Whether the vote is in favor or not.
 * @param {VotingTypes.Motion} motion - The motion being voted on.
 * @return {void}
 */
const visualizeVote = async (inFavor: boolean, motion: VotingTypes.Motion) => {
    // Get the number of votes cast
    let votesCast = motion.votes;

    // Calculate the cost based on the number of votes cast
    let cost = calculateCost(motion.votes);

    // If the motion does not have visual credits, create an empty array
    if (!motion.visualCredits) {
        motion.visualCredits = [];
    }

    // Calculate the number of credits to visualize and the number of already visualized credits
    let creditsToVisualize = cost;
    let creditsAlreadyVisualized = motion.visualCredits.length;

    // If there are more credits to visualize than already visualized
    if (creditsToVisualize > creditsAlreadyVisualized) {
        // Get the difference between the two numbers
        let difference = creditsToVisualize - creditsAlreadyVisualized;

        // Pop the credits from the visualCredits array and push them to the motion.visualCredits array
        for (let i = 0; i < difference; i++) {
            const credit = visualCredits.value.pop();

            if (credit) {
                motion.visualCredits.push(credit);
            }
        }

        // Cache the length of the visualCredits array in memory
        const visualCreditsLength = motion.visualCredits.length;

        // Iterate over the visualCredits array using a for...of loop
        let visualIndex = 1;
        for (const credit of motion.visualCredits) {
            const creditCode = credit.creditCode;

            // Determine whether the vote is in favor or not
            const isFavor = votesCast > 0 ? "f" : "o";

            // Generate the target code for the credit
            const targetCode = `d-${isFavor}-${motion.uuid}-${visualIndex}`;

            // If the target code is different from the credit code, move the credit
            if (targetCode !== creditCode) {
                moveCredit(creditCode, targetCode);
            }

            visualIndex++;
        }
    }


    // If there are fewer credits to visualize than already visualized
    else if (creditsToVisualize < creditsAlreadyVisualized) {
        // Get the difference between the two numbers
        let difference = creditsAlreadyVisualized - creditsToVisualize;

        // Pop the credits from the motion.visualCredits array and push them to the visualCredits.value array
        for (let i = 0; i < difference; i++) {
            const credit = motion.visualCredits.pop();

            if (credit) {
                visualCredits.value.push(credit);
            }
        }

        // Cache the length of the visualCredits array
        const visualCreditsLength = visualCredits.value.length;

        // Iterate over the visualCredits array using a for...of loop
        let visualIndex = 1;
        for (const credit of visualCredits.value) {
            const creditCode = credit.creditCode;

            // Determine whether the vote is in favor or not
            const isFavor = votesCast > 0 ? "f" : "o";

            // Generate the target code for the credit
            const targetCode = `bg-${visualIndex}`;

            // If the target code is different from the credit code, move the credit
            if (targetCode !== creditCode) {
                moveCredit(creditCode, targetCode);
            }

            visualIndex++;
        }
    }
};


/**
 * Moves a credit to a new position on the page.
 * @param {string} selectedCredit - The credit to move.
 * @param {string} toCode - The target code for the credit.
 */
const moveCredit = (selectedCredit: string, toCode: string) => {
    // select the credit with data-creditCode c-25
    const creditToMove = document.querySelector(`[data-creditCode="${selectedCredit}"]`);

    if (!creditToMove) {
        console.log('no credit to move');
        return;
    }

    // change the targetCode to d-5
    creditToMove.setAttribute('data-targetCode', `${toCode}`);

    creditToMove.classList.add('in-transition');

    setTimeout(() => {
        creditToMove.classList.remove('in-transition');
    }, 600);
}


/**
 * Updates the position of movable credits on the page.
 * @returns {Promise<void>}
 */
const updateCredits = async () => {

    // Get all movable credits
    const movableCredits = document.querySelectorAll('.movable');

    // Iterate over all movable credits
    movableCredits.forEach((credit, index) => {

        // Get the target code
        const targetCode = credit.getAttribute('data-targetCode');

        // Get the target position
        const targetPosition = document.querySelector(`[data-creditCode="${targetCode}"]`);

        // If there is no target position, log an error and return
        if (!targetPosition) {
            console.log('no target position', targetCode);
            return;
        }

        // Calculate the target position relative to the component
        const targetRect = targetPosition.getBoundingClientRect();
        let targetTop = targetRect.top;
        let targetLeft = targetRect.left;

        // If the target code starts with 'd', adjust the position by 3 pixels
        if (targetCode && targetCode.startsWith('d')) {
            targetTop += 3;
            targetLeft += 3;
        }

        // Set the position of the credit
        (credit as HTMLElement).style.position = 'absolute';
        (credit as HTMLElement).style.top = `${targetTop}px`;
        (credit as HTMLElement).style.left = `${targetLeft}px`;

    });

    // Request the next animation frame to update the credits again
    requestAnimationFrame(updateCredits);

}


/**
 * Computed property that calculates the total number of votes cast in the election.
 * @returns {number} The total number of votes cast.
 */
const votesCast = computed(() => {
    let votesCast = 0;

    Vote.value.motions.forEach((motion) => {
        votesCast += Math.abs(motion.votes);
    });

    return votesCast;
});

</script>


<template>

    <Head :title="($page.props.election as VotingTypes.Election).name" />

    <FrontLayout>
        <MovingCredits :credits="Election.credits"></MovingCredits>
        <Tutorial :credits="Election.credits"></Tutorial>

        <section class="election-info">
            <h1>{{ Election.name }}</h1>
            <p>{{ Election.description }}</p>

            <br />
            <span>Link to this election: <a :href="route('election.vote', { uuid: Election.uuid })">{{
                route('election.vote', {
                    uuid: Election.uuid
                    }) }}</a></span>
            <label>Your name: <em>(not required)</em></label>
            <input type="text" v-model="Vote.name" />
        </section>

        <section class="election">


            <div class="left-sidebar sidebar">
                <h2>Credits remaining:</h2>
                <h2>{{ Vote.remainingCredits }}</h2>

                <h3>Max Credits: {{ Election.credits }}</h3>

                <AllCredits :credits="Election.credits"></AllCredits>
                <button class="reset-button" @click="resetCredits()">Reset</button>
                <br />
                <table class="cost-calculation">
                    <thead>
                        <tr>
                            <th colspan="2">Votes to Credits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="v in calculateVotesInterger(Election.credits)" :key="v">
                            <td>{{ v }} {{ v == 1 ? 'vote:' : 'votes:' }}</td>
                            <td>{{ calculateCost(v) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <main>
                <div class="motions">

                    <div class="motion" v-for="(motion, index) in Vote.motions" :key="index">
                        <div class="motion-header">
                            <h2>{{ motion.content }}</h2>
                            <span>{{ Math.abs(motion.votes) }} Votes | Credits spent: {{ calculateCost(motion.votes)
                                }}</span>
                        </div>


                        <div class="motion-body">
                            <div class="motion-body-container favor">
                                <VoteVisualizer :code="`f-${motion.uuid}`" :credits="Election.credits"
                                    :force-spread="Election.options.forceSpread"></VoteVisualizer>
                            </div>

                            <div class="motion-body-container container-center">
                                <div class="container-center-body">
                                    <div class="question-number">
                                        <strong>Issue #{{ index + 1 }}</strong>
                                    </div>

                                    <div class="button-containers">
                                        <div class="favor button-container">
                                            <button @click="castVote(motion, true)">
                                                {{ motion.votes >= 0 ? (motion.votes === 0 ? 'Vote' : 'More') : 'Fewer'
                                                }}
                                            </button>
                                            <strong>In favor</strong>
                                        </div>

                                        <div class="line"></div>

                                        <div class="opposed button-container">

                                            <button @click="castVote(motion, false)">
                                                {{ motion.votes >= 0 ? (motion.votes === 0 ? 'Vote' : 'Fewer') : 'More'
                                                }}
                                            </button>
                                            <strong>Opposed</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="motion-body-container opposed">
                                <VoteVisualizer :code="`o-${motion.uuid}`" :opposed="true" :credits="Election.credits"
                                    :force-spread="Election.options.forceSpread">
                                </VoteVisualizer>
                            </div>
                        </div>
                    </div>
                </div>

            </main>

            <div class="right-sidebar sidebar">
                <div class="results">
                    <h2>My votes:</h2>

                    <div class="motions-results">

                        <div class="motion-result">
                            <span>In favor</span>
                            <span>Issue</span>
                            <span>Opposed</span>
                        </div>

                        <div class="motion-result" v-for="(motion, index) in Vote.motions" :key="index">
                            <span>{{ motion.votes > 0 ? motion.votes : "-" }} </span>
                            <span>#{{ index + 1 }}</span>
                            <span>{{ motion.votes < 0 ? Math.abs(motion.votes) : "-" }}</span>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm">
                        <button type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </section>

    </FrontLayout>
</template>

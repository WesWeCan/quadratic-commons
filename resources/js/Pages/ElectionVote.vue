<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref } from 'vue';
import { Link, Head, useForm, usePage } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';
import AllCredits from '@/Components/Visualizer/AllCredits.vue';
import VoteVisualizer from '@/Components/Visualizer/VoteVisualizer.vue';



const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);
const Vote = ref<VotingTypes.Vote>({
    name: 'Annonymous',
    remainingCredits: JSON.parse(JSON.stringify(Election.value.credits)),
    motions: JSON.parse(JSON.stringify(Election.value.motions)),
    election_uuid: Election.value.uuid,
});

const form = useForm(Vote.value);



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


const calculateCost = (votes: number) => {

    let numberOfVotes = votes;

    let cost = Math.pow(numberOfVotes, 2);
    // console.log(numberOfVotes, cost);

    return cost;
};





onMounted(() => {
    console.log('Index.vue mounted');

    Vote.value = {
        name: 'Annonymous',
        remainingCredits: JSON.parse(JSON.stringify(Election.value.credits)),
        motions: JSON.parse(JSON.stringify(Election.value.motions)),
        election_uuid: Election.value.uuid,
    }

    initCredits();


    requestAnimationFrame(updateCredits);

});



const submitForm = () => {
    console.log('submitForm');

    form.election_uuid = Election.value.uuid;
    form.name = Vote.value.name;
    form.remainingCredits = Vote.value.remainingCredits;
    form.motions = Vote.value.motions;

    form.post(route('vote.store'))
}



const visCreditsTotal = ref(Election.value.credits);

const visualCredits = ref([] as VotingTypes.Credit[]);

const initCredits = () => {

    console.log('initCredits');

    for (let i = 1; i <= Election.value.credits; i++) {
        visualCredits.value.push({
            creditCode: `c-${i}`,
            targetCode: `bg-${i}`,
        });
    }

}



const visualizeVote = async (inFavor: boolean, motion: VotingTypes.Motion) => {
    // console.log('visualizeVote');

    let remainingCredits = Vote.value.remainingCredits;

    let votesCast = motion.votes;
    let cost = calculateCost(motion.votes);


    if (!motion.visualCredits) {
        motion.visualCredits = [];
    }


    let creditsToVisualize = cost;
    let creditsAlreadyVisualized = motion.visualCredits.length;


    // console.log("creditsToVisualize", creditsToVisualize);
    // console.log("creditsAlreadyVisualized", creditsAlreadyVisualized);

    if (creditsToVisualize > creditsAlreadyVisualized) {
        // there are more credits to visualize than already visualized
        // get them from the visualCredits array


        let difference = creditsToVisualize - creditsAlreadyVisualized;

        for (let i = 0; i < difference; i++) {
            const credit = visualCredits.value.pop();

            if (credit) {
                motion.visualCredits.push(credit);
            }
        }

        // All Arrays are prepared
        // now we can move the credits
        // get all credits that are not in the right place
        for (let i = 0; i < motion.visualCredits.length; i++) {

            const credit = motion.visualCredits[i];
            const creditCode = credit.creditCode;

            const isFavor = votesCast > 0 ? "f" : "o";

            // const visualIndex = i + 1;
            const visualIndex = calcVisualIndex(i, motion.votes);
            // const visualIndex = lookupIndex(i);

            const targetCode = `d-${isFavor}-${motion.uuid}-${visualIndex}`;


            if (targetCode !== creditCode) {
                moveCredit(creditCode, targetCode);
            }

        }

    }
    else if (creditsToVisualize < creditsAlreadyVisualized) {
        // there are more credits to visualize than already visualized
        // get them from the visualCredits array


        let difference = creditsAlreadyVisualized - creditsToVisualize;

        for (let i = 0; i < difference; i++) {
            const credit = motion.visualCredits.pop();

            if (credit) {
                visualCredits.value.push(credit);
            }

        }




        // All Arrays are prepared
        // now we can move the credits
        // get all credits that are not in the right place
        for (let i = 0; i < visualCredits.value.length; i++) {

            const credit = visualCredits.value[i];
            const creditCode = credit.creditCode;

            const isFavor = votesCast > 0 ? "f" : "o";

            const visualIndex = i + 1;

            const targetCode = `bg-${visualIndex}`;

            if (targetCode !== creditCode) {
                moveCredit(creditCode, targetCode);
            }

        }






    }

};










function calcVisualIndex(targetIndex: number, votes: number) {
    // targetIndex = targetIndex + 1;

    return targetIndex + 1;

    let maxCredits = Election.value.credits;
    let maxColumns = Math.sqrt(maxCredits);


    console.log(targetIndex, maxColumns, votes);

    let columns = votes;
    let rows = votes;


    let cellNumber = 0;


 let arr = [];
  let count = 1;
  for (let i = 0; i < rows; i++) {
    let row = [];

    let addition = i * 10;

    for (let j = 0; j < columns; j++) {
      row.push((j+1) + addition);
    }

    arr.push(row);
  }


  arr = arr.flat();

  return arr[targetIndex];



}









const moveCredit = (selectedCredit: string, toCode: string) => {
    // console.log('moveCredit');


    // select the credit with data-creditCode c-25
    const creditToMove = document.querySelector(`[data-creditCode="${selectedCredit}"]`);
    // console.log(creditToMove);


    if (!creditToMove) {
        console.log('no credit to move');
        return;
    }

    // change the targetCode to d-5
    creditToMove.setAttribute('data-targetCode', `${toCode}`);


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
            console.log('no target position', targetCode);
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


</script>


<template>
    <Head :title="($page.props.election as VotingTypes.Election).name" />

    <FrontLayout>


        <h1>{{ Election.name }}</h1>

        <label>My Name:</label>
        <input type="text" v-model="Vote.name" />

        <section class="election">


            <div class="left-sidebar sidebar">
                <button @click="moveCredit">Move Credit</button>


                <h2>Max Credits: {{ Election.credits }}</h2>

                <h2>Remaining Credits: {{ Vote.remainingCredits }}</h2>

                <AllCredits :credits="Election.credits"></AllCredits>


                <br />

                <div class="cost-calculation">
                    <span v-for="v in Math.sqrt(Election.credits)" :key="v">
                        {{ `${v} votes - ${calculateCost(v)} credits` }}
                    </span>
                </div>

                <!-- <pre>{{
                    visualCredits
                }}</pre> -->
            </div>

            <main>
                <div class="motions">

                    <div class="motion" v-for="(motion, index) in Vote.motions" :key="index">




                        <div class="motion-header">
                            <span>Question {{ index + 1 }} of {{ Vote.motions.length }}</span>

                            <strong>{{ motion.content }}</strong>
                        </div>


                        <div class="motion-body">
                            <VoteVisualizer :code="`f-${motion.uuid}`" :credits="Election.credits"></VoteVisualizer>


                            <div class="motion-vote-container">


                                <div class="favor button-container">
                                    <button @click="castVote(motion, true)">In favor</button>
                                    <span>{{ motion.votes }} votes</span>
                                    <span>Cost: {{ calculateCost(motion.votes) }}</span>
                                </div>

                                <div class="opposed button-container">
                                    <button @click="castVote(motion, false)">Opposed</button>
                                    <span>{{ motion.votes }} votes</span>
                                    <span>Cost: {{ calculateCost(motion.votes) }}</span>
                                </div>

                            </div>


                            <VoteVisualizer :code="`o-${motion.uuid}`" :opposed="true" :credits="Election.credits"></VoteVisualizer>
                        </div>

                        <!--
                        <pre>{{ motion.visualCredits?.length || 0 }} {{ motion.visualCredits }}</pre> -->

                    </div>
                </div>

                <form @submit.prevent="submitForm">
                    <!-- <pre>
{{ form.errors }}
</pre> -->

                    <button type="submit">Vote</button>
                </form>
            </main>

            <div class="right-sidebar sidebar">
                <div class="results">
                    <h1>My Vote</h1>

                    <div class="motions-results">

                        <div class="motion-result" v-for="(motion, index) in Vote.motions" :key="index">
                            {{ motion.content }} | {{ motion.votes }} votes {{ calculateCost(motion.votes) }} credits
                        </div>
                    </div>





                </div>
            </div>
        </section>

    </FrontLayout>
</template>



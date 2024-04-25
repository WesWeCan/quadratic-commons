<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref, computed } from 'vue';
import { Link, Head, useForm, usePage, router } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';

import SvgIcon from '@jamescoyle/vue-icon';
import { mdiThumbDown, mdiThumbUp, mdiThumbsUpDown, mdiTriangle } from '@mdi/js';


const maxCredits = ref<number>(100);
const credits = ref<number>(100);

const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);

const myUuid = ref<string>();
const myVoteCode = ref<string>();

/**
 * Navigates to the result page if a vote code is provided.
 *
 * @return {void}
 */
const goToResult = () => {

    if (!myVoteCode.value) {
        return;
    }

    let toRoute = route('election.results.code', { uuid: page.props.election.uuid, votecode: myVoteCode.value });

    router.visit(toRoute);
};

/**
 * Copies the code stored in `page.props.myVoteCode` to the clipboard and displays an alert message.
 *
 * @return {undefined} No return value.
 */
const copyCode = () => {

    if (!page.props.myVoteCode) {
        return;
    }


    navigator.clipboard.writeText(page.props.myVoteCode);

    alert('copied');
};


/**
 * Calculates the netto votes based on the given array of vote results.
 *
 * @param {VotingTypes.VoteResult[]} votes - An array of vote results.
 * @return {number} The total netto votes.
 */
const nettoVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    // console.log(votes);

    votes.forEach((vote) => {
        voteResult += vote.votes;
    });

    return voteResult;
};


/**
 * Calculates the total number of votes based on the vote results.
 *
 * @param {VotingTypes.VoteResult[]} votes - An array of vote results.
 * @return {number} The total number of votes.
 */
const brutoVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        voteResult += Math.abs(vote.votes);
    });

    return voteResult;
};


/**
 * Calculates the number of voters who cast a vote in a given motion.
 *
 * @param {VotingTypes.VoteResult[]} votes - An array of vote results.
 * @param {VotingTypes.Motion} motion - The motion being voted on.
 * @return {number} The total number of voters who cast a vote.
 */
const numVoters = (votes: VotingTypes.VoteResult[], motion: VotingTypes.Motion) => {
    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes !== 0) {
            voteResult++;
        }
    });

    return voteResult;

}


/**
 * Calculates the total number of in-favor votes from an array of vote results.
 *
 * @param {VotingTypes.VoteResult[]} votes - An array of vote results.
 * @return {number} The total number of in-favor votes.
 */
const inFavorVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes > 0) {
            voteResult += vote.votes;
        }
    });

    return voteResult;
};


/**
 * Calculates the sum of all negative votes in the given array of vote results.
 *
 * @param {VotingTypes.VoteResult[]} votes - The array of vote results.
 * @return {number} The sum of all negative votes.
 */
const opposedVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes < 0) {
            voteResult += vote.votes;
        }
    });

    return voteResult;
};


/**
 * Calculates the cumulative credits based on the votes and whether they are in favor or not.
 *
 * @param {VotingTypes.VoteResult[]} votes - The array of voting results.
 * @param {boolean} inFavor - Indicates whether the votes are in favor or not.
 * @return {number} The cumulative number of credits.
 */
const calcCumulativeCredits = (votes: VotingTypes.VoteResult[], inFavor: boolean) => {

    let credits = 0;

    if (inFavor) {
        votes.forEach((vote) => {
            if (vote.votes > 0) {
                credits += calcCredits(vote.votes);
            }
        });
    } else {
        votes.forEach((vote) => {
            if (vote.votes < 0) {
                credits += Math.abs(calcCredits(vote.votes));
            }
        });
    }

    return credits;
}


/**
 * Returns an array of vote results corresponding to a specific election motion.
 *
 * @param {VotingTypes.Motion} elctionMotion - The election motion to search for in the votes.
 * @return {VotingTypes.VoteResult[]} An array of vote results matching the election motion.
 */
const motionVoted = (elctionMotion: VotingTypes.Motion) => {


    if (!Election.value.votes) {
        return [];
    }

    let votes: VotingTypes.VoteResult[] = [];
    Election.value.votes.forEach((vote) => {
        vote.motions.forEach((voteMotion) => {
            if (voteMotion.uuid === elctionMotion.uuid) {



                let voteObj: VotingTypes.VoteResult = {
                    vote_uuid: vote.uuid || '',
                    votes: voteMotion.votes,
                    voted_by: vote.name,
                    credits: calcCredits(voteMotion.votes)
                };

                votes.push(voteObj);

            }
        });

    });

    return votes;
};


const calcCredits = (votes: number) => {
    return Math.pow(votes, 2);
}


/**
 * This function calculates the election results data.
 *
 * @returns The election results data.
 */
const ElectionResultsData = () => {
    // Array to store motion results
    let motions = [] as VotingTypes.MotionResult[];

    // Iterate over each motion in the election
    Election.value.motions.forEach((motion) => {
        // Get the votes for the motion
        const votes = motionVoted(motion);

        // Create a motion result object
        let motionResult: VotingTypes.MotionResult = {
            motion_content: (motion as VotingTypes.Motion).content,
            motion_uuid: (motion as VotingTypes.Motion).uuid,
            votes: votes,
            numVoters: numVoters(votes, motion),
            nettoVotes: nettoVotes(votes),
            brutoVotes: brutoVotes(votes),
            inFavorVotes: inFavorVotes(votes),
            opposedVotes: opposedVotes(votes),
            inFavorCredits: calcCumulativeCredits(votes, true),
            opposedCredits: calcCumulativeCredits(votes, false),
            totalCreditsSpent: calcCumulativeCredits(votes, true) + calcCumulativeCredits(votes, false)
        };

        // Add the motion result to the array
        motions.push(motionResult);
    });

    // If there are no votes, return null
    if (!Election.value.votes) {
        return null;
    }

    // Calculate the overall election results
    let results: VotingTypes.ElectionResult = {
        motions: motions,
        numVoters: Election.value.votes.length,
        creditsAvailable: maxCredits.value,
        creditsSpend: motions.reduce((a, b) => a + b.totalCreditsSpent, 0),
        inFavorCredits: motions.reduce((a, b) => a + b.inFavorCredits, 0),
        opposedCredits: motions.reduce((a, b) => a + b.opposedCredits, 0),
        inFavorVotes: motions.reduce((a, b) => a + b.inFavorVotes, 0),
        opposedVotes: motions.reduce((a, b) => a + b.opposedVotes, 0),
        totalCreditsSpent: motions.reduce((a, b) => a + b.totalCreditsSpent, 0),

        // Get the motion with the highest netto votes
        nettoWinner: motions.reduce((a, b) => a.nettoVotes > b.nettoVotes ? a : b).motion_uuid,
        // Get the motion with the lowest netto votes
        nettoLoser: motions.reduce((a, b) => a.nettoVotes < b.nettoVotes ? a : b).motion_uuid,
        // Get the motion with the most total credits spent
        mostAttention: motions.reduce((a, b) => a.totalCreditsSpent > b.totalCreditsSpent ? a : b).motion_uuid,
    }

    // Return the election results
    return results;
};


/**
 * Retrieves the vote for a specific motion based on the motion UUID and the user's vote UUID.
 *
 * @param {string} motionUuid - The UUID of the motion.
 * @returns {object|null} - The vote object if found, otherwise null.
 */
const UuidVote = (motionUuid: string) => {

    const myUuid = page.props.myVoteUuid;

    let results = ElectionResultsData();

    if (!results) {
        return null;
    }

    // Find the motion that matches the motionUuid
    let motion = results.motions.find((motion) => {
        return motion.motion_uuid === motionUuid;
    });

    if (!motion) {
        return null;
    }

    // Get the vote from the votes array of myUuid
    let vote = motion.votes.find((vote) => {
        return vote.vote_uuid === myUuid;
    });

    if (!vote) {
        return null;
    }

    return vote;
};



const sortNetto = ref(true);
const showStats = ref(true);
const limitResults = ref(false);


/**
 * Sorts and limits the election results based on user preferences.
 *
 * @returns {object|null} - The sorted and limited election results if available, otherwise null.
 */
const sortedResults = computed(() => {

    // Get the election results data
    let results = ElectionResultsData();

    // If there are no results, return null
    if (!results) {
        return null;
    }

    // Sort the motions based on nettoVotes or totalCreditsSpent
    if (sortNetto.value) {
        // Sort by nettoVotes
        results.motions.sort((a, b) => {
            return b.nettoVotes - a.nettoVotes;
        });
    }
    else {
        // Sort by totalCreditsSpent
        results.motions.sort((a, b) => {
            return b.totalCreditsSpent - a.totalCreditsSpent;
        });
    }

    // Limit the number of results if limitResults is true
    if (limitResults.value) {
        // Slice the motions array to get the first 3 elements
        results.motions = results.motions.slice(0, 3);
    }

    // Return the sorted results
    return results;

});




</script>


<template>
    <Head :title="($page.props.election as VotingTypes.Election).name" />

    <FrontLayout>
        <div class="election-results">
            <div class="election-results__header">
                <h1>Results: {{ Election.name }}</h1>
                <p>{{ Election.description }}</p>
            </div>

            <div v-if="$page.props.myVoteCode">
                <form>
                    <h2>My vote code</h2>
                    <p>
                        Share this code / link with others to compare your votes.
                    </p>
                    <input type="text" :disabled="true" :value="$page.props.myVoteCode" />
                    <Link :href="route('election.results.code', { uuid: $page.props.election.uuid, votecode: $page.props.myVoteCode })">{{ route('election.results.code', { uuid: $page.props.election.uuid, votecode: $page.props.myVoteCode }) }}</Link>
                    <button @click.prevent="() => copyCode()">Copy</button>
                </form>
            </div>

            <div v-if="!$page.props.myVoteUuid">
                <form @submit.prevent="goToResult()">
                    <label>Enter you vote code to compare your votes</label>
                    <input type="text" v-model="myVoteCode" />
                    <button type="submit">Go</button>
                </form>
            </div>

            <div class="election-results__body">




                <div class="results__result">



                    <div class="global">
                        <h1>Community voted: ({{ $page.props.election.votes.length }} voters)</h1>
                        <!-- <button @click="showStats = !showStats">{{ showStats ? "Hide" : "Show" }} statistics</button> -->
                        <!-- <button @click="sortNetto = !sortNetto">{{ !sortNetto ? "Sort based on votes" : "Sort based on attention" }}</button>
                        <button @click="limitResults = !limitResults">{{ limitResults ? "Show all issues" : "Limit to 3 issues" }}</button> -->
                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">

                        <h1>My Votes</h1>


                    </div>

                </div>

                <div class="results__result" v-for="motion in sortedResults.motions" :key="motion.motion_uuid"
                    v-if="sortedResults">

                    <!-- {{ motion }} -->
                    <div class="global">
                        <h2>{{ motion.motion_content }}
                            <!-- <svg-icon :size="18" type="mdi" :path="mdiThumbUp"
                                v-if="sortedResults.nettoWinner === motion.motion_uuid"
                                v-tooltip="`Highest netto votes`"></svg-icon>
                            <svg-icon :size="18" type="mdi" :path="mdiThumbDown"
                                v-if="sortedResults.nettoLoser === motion.motion_uuid"
                                v-tooltip="`Lowest netto votes`"></svg-icon> -->
                            <svg-icon :size="18" type="mdi" :path="mdiThumbsUpDown"
                                v-if="sortedResults.mostAttention === motion.motion_uuid"
                                v-tooltip="`Most attention based on credits`"></svg-icon>
                        </h2>



                        <div class="vote-data" v-if="showStats">

                            <span>Voting outcome: <strong>{{ motion.nettoVotes }}</strong> (Total credits spent on this issue: <strong>{{ motion.totalCreditsSpent }}</strong>)</span>

                            <details>

                                <summary><SvgIcon type="mdi" :path="mdiTriangle" :size="24" class="arrow" /></summary>

                                <div>
                                    <span>Number of voters: <strong>{{ motion.numVoters }}</strong></span>
                                    <span>Votes in favor: <strong v-tooltip="`${motion.inFavorCredits} credits spent`">{{ motion.inFavorVotes }}</strong> | Votes opposed: <strong v-tooltip="`${motion.opposedCredits} credits spent`">{{ motion.opposedVotes }}</strong> (Gross votes: <strong>{{ motion.brutoVotes }}</strong>)</span>
                                </div>


                            </details>

                        </div>

                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">
                        <h2>{{ motion.motion_content }}</h2>


                        <div class="vote-data" v-if="showStats">

                            <div>
                                <span v-tooltip="`Netto votes: in favor minus opposed votes`">{{
                                    UuidVote(motion.motion_uuid)?.votes }} Votes</span>
                                <em>{{ UuidVote(motion.motion_uuid)?.credits
                                }} Credits spent</em>
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>




    </FrontLayout></template>

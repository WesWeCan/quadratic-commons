<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref, computed } from 'vue';
import { Link, Head, useForm, usePage, router } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';


const maxCredits = ref<number>(100);
const credits = ref<number>(100);

const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);


const myUuid = ref<string>();
const myVoteCode = ref<string>();

const calculateCost = (votes: number) => {

    let numberOfVotes = votes;

    let cost = Math.pow(numberOfVotes, 2);
    console.log(numberOfVotes, cost);

    return cost;
};





onMounted(() => {

});


const nettoVotes = (votes: any[]) => {

    let voteResult = 0;

    console.log(votes);

    votes.forEach((vote) => {
        voteResult += vote.votes;
    });

    return voteResult;
};

const inFavorVotes = (votes: any[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes > 0) {
            voteResult += vote.votes;
        }
    });

    return voteResult;
};

const opposedVotes = (votes: any[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes < 0) {
            voteResult += vote.votes;
        }
    });

    return voteResult;
};


const motionVoted = (elctionMotion: VotingTypes.Motion) => {


    if (!Election.value.votes) {
        return false;
    }

    // get all the vote objects that voted for this motion
    // get the specific motion from the vote object
    // the vote object should only contain:
    // - the motion uuid as motion_uuid
    // - the vote uuid as vote_uuid
    // - the number of votes as votes
    // - the name of the voter as voted_by


    let votes = [];
    Election.value.votes.forEach((vote) => {
        vote.motions.forEach((voteMotion) => {
            if (voteMotion.uuid === elctionMotion.uuid) {
                let voteObj = {
                    vote_uuid: vote.uuid,
                    votes: voteMotion.votes,
                    voted_by: vote.name,
                };


                votes.push(voteObj);


            }
        });




    });






    return votes;
};


const nettoCredits = (elctionMotion: VotingTypes.Motion) => {

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


const calcCredits = (votes: number) => {
    return Math.pow(votes, 2);
}


const UuidVote = (motionUuid: string) => {

    const myUuid = page.props.myVoteUuid;

    let results = {
        motions: [] as any[]
    };

    Election.value.motions.forEach((motion) => {

        let motionResult = {
            motion_content: (motion as VotingTypes.Motion).content,
            motion_uuid: (motion as VotingTypes.Motion).uuid,
            votes: motionVoted(motion),
        };

        motionResult.nettoVotes = nettoVotes(motionResult.votes);
        motionResult.inFavorVotes = inFavorVotes(motionResult.votes);
        motionResult.opposedVotes = opposedVotes(motionResult.votes);


        motionResult.inFavorCredits = calcCredits(motionResult.inFavorVotes);
        motionResult.opposedCredits = calcCredits(motionResult.opposedVotes);
        motionResult.totalCreditsSpend = motionResult.inFavorCredits + motionResult.opposedCredits;


        results.motions.push(motionResult);

    });



    // find the motion that matches the motionUuid and get the vote from votes array of myUuid
    let motion = results.motions.find((motion) => {
        return motion.motion_uuid === motionUuid;
    });

    if (!motion) {
        return 0;
    }

    let vote = motion.votes.find((vote) => {
        return vote.vote_uuid === myUuid;
    });


    if (!vote) {
        return 0;
    }


    return vote;




};


const ElectionResults = computed(() => {



    let results = {
        motions: [] as any[]
    };

    Election.value.motions.forEach((motion) => {

        let motionResult = {
            motion_content: (motion as VotingTypes.Motion).content,
            motion_uuid: (motion as VotingTypes.Motion).uuid,
            votes: motionVoted(motion),
        };

        motionResult.nettoVotes = nettoVotes(motionResult.votes);
        motionResult.inFavorVotes = inFavorVotes(motionResult.votes);
        motionResult.opposedVotes = opposedVotes(motionResult.votes);


        motionResult.inFavorCredits = calcCredits(motionResult.inFavorVotes);
        motionResult.opposedCredits = calcCredits(motionResult.opposedVotes);
        motionResult.totalCreditsSpend = motionResult.inFavorCredits + motionResult.opposedCredits;


        results.motions.push(motionResult);

    });



    let sortedResults = results.motions.sort((a, b) => {
        return b.nettoVotes - a.nettoVotes;
    });



    return sortedResults;



});


const goToResult = () => {

    if (!myVoteCode.value) {
        return;
    }

    let toRoute = route('election.results.code', { uuid: page.props.election.uuid, votecode: myVoteCode.value }); // 'https://ziggy.test/posts/1'


    router.visit(toRoute);
};

const copyCode = () => {
    navigator.clipboard.writeText(page.props.myVoteCode);

    alert('copied');
};

</script>


<template>
    <Head :title="($page.props.election as VotingTypes.Election).name" />

    <FrontLayout>


        <!-- ranking -->
        <!-- percentages  -->







        <div class="election-results">


            <div class="election-results__header">
                <h1>Results: {{ Election.name }}</h1>
                <p>{{ Election.description }}</p>
            </div>


            <div v-if="$page.props.myVoteCode">

            <form>
                <label>My vote code</label>
                <input type="text" :disabled="true" :value="$page.props.myVoteCode" />
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
                        <h1>Community voted</h1>




                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">

                        <h1>I Voted</h1>


                    </div>



                </div>

                <div class="results__result" v-for="motion in ElectionResults" :key="motion.motion_uuid">


                    <div class="global">
                        <h2>{{ motion.motion_content }}</h2>
                        <h3>{{ motion.nettoVotes }} Votes | <em>{{ motion.totalCreditsSpend }} Credits</em></h3>

                        <span>in favor: {{ motion.inFavorVotes }} | <em>{{ motion.inFavorCredits }} Credits</em></span>
                        <span>opposed: {{ motion.opposedVotes }} | <em>{{ motion.opposedCredits }} Credits</em></span>
                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">

                        <h2>{{ motion.motion_content }}</h2>
                        <h3>{{ UuidVote(motion.motion_uuid).votes }} Votes | <em>{{ motion.totalCreditsSpend }} Credits</em>
                        </h3>


                    </div>



                </div>



            </div>



        </div>





<!--
        <pre>
        {{ $page.props.myVoteUuid }}
        </pre>



        <pre>
        {{ ElectionResults }}
        </pre>


        <pre>
        {{ $page.props.election }}
        </pre> -->





    </FrontLayout>
</template>



<style scoped></style>

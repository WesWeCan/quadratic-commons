<script setup lang="ts">

import FrontLayout from '@/Layouts/FrontLayout.vue';

import { onMounted, ref, computed } from 'vue';
import { Link, Head, useForm, usePage, router } from '@inertiajs/vue3';

import * as VotingTypes from '@/types/voting-types';

import SvgIcon from '@jamescoyle/vue-icon';
import { mdiThumbDown, mdiThumbUp, mdiThumbsUpDown } from '@mdi/js';


const maxCredits = ref<number>(100);
const credits = ref<number>(100);

const page = usePage();

const Election = ref<VotingTypes.Election>(page.props.election as VotingTypes.Election);


const myUuid = ref<string>();
const myVoteCode = ref<string>();

const goToResult = () => {

    if (!myVoteCode.value) {
        return;
    }

    let toRoute = route('election.results.code', { uuid: page.props.election.uuid, votecode: myVoteCode.value });


    router.visit(toRoute);
};

const copyCode = () => {

    if (!page.props.myVoteCode) {
        return;
    }


    navigator.clipboard.writeText(page.props.myVoteCode);

    alert('copied');
};



const nettoVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    // console.log(votes);

    votes.forEach((vote) => {
        voteResult += vote.votes;
    });

    return voteResult;
};


const brutoVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        voteResult += Math.abs(vote.votes);
    });

    return voteResult;
};


const numVoters = (votes: VotingTypes.VoteResult[], motion: VotingTypes.Motion) => {


    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes !== 0) {
            voteResult++;
        }
    });

    return voteResult;


}


const inFavorVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes > 0) {
            voteResult += vote.votes;
        }
    });

    return voteResult;
};

const opposedVotes = (votes: VotingTypes.VoteResult[]) => {

    let voteResult = 0;

    votes.forEach((vote) => {
        if (vote.votes < 0) {
            voteResult += vote.votes;
        }
    });

    return voteResult;
};


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



const ElectionResultsData = () => {



    let motions = [] as VotingTypes.MotionResult[]


    Election.value.motions.forEach((motion) => {

        const votes = motionVoted(motion);
        //     ^?

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
            totalCreditsSpend: calcCumulativeCredits(votes, true) + calcCumulativeCredits(votes, false)
        };

        motions.push(motionResult);

    });

    if (!Election.value.votes) {
        return null;
    }

    let results: VotingTypes.ElectionResult = {

        motions: motions,
        numVoters: Election.value.votes.length,
        creditsAvailable: maxCredits.value,
        creditsSpend: motions.reduce((a, b) => a + b.totalCreditsSpend, 0),
        inFavorCredits: motions.reduce((a, b) => a + b.inFavorCredits, 0),
        opposedCredits: motions.reduce((a, b) => a + b.opposedCredits, 0),
        inFavorVotes: motions.reduce((a, b) => a + b.inFavorVotes, 0),
        opposedVotes: motions.reduce((a, b) => a + b.opposedVotes, 0),
        totalCreditsSpend: motions.reduce((a, b) => a + b.totalCreditsSpend, 0),

        nettoWinner: motions.reduce((a, b) => a.nettoVotes > b.nettoVotes ? a : b).motion_uuid,
        nettoLoser: motions.reduce((a, b) => a.nettoVotes < b.nettoVotes ? a : b).motion_uuid,
        mostAttention: motions.reduce((a, b) => a.totalCreditsSpend > b.totalCreditsSpend ? a : b).motion_uuid,
    }


    return results;

};


const UuidVote = (motionUuid: string) => {

    const myUuid = page.props.myVoteUuid;

    let results = ElectionResultsData();

    if (!results) {
        return null;
    }

    // find the motion that matches the motionUuid and get the vote from votes array of myUuid
    let motion = results.motions.find((motion) => {
        return motion.motion_uuid === motionUuid;
    });

    if (!motion) {
        return null;
    }

    let vote = motion.votes.find((vote) => {
        return vote.vote_uuid === myUuid;
    });


    if (!vote) {
        return null;
    }


    return vote;

};


const sortNetto = ref(true);
const showStats = ref(false);
const limitResults = ref(true);

const sortedResults = computed(() => {

    let results = ElectionResultsData();

    if (!results) {
        return null;
    }


    if (sortNetto.value) {
        results.motions.sort((a, b) => {
            return b.nettoVotes - a.nettoVotes;
        });
    }
    else {
        // based on credits
        results.motions.sort((a, b) => {
            return b.totalCreditsSpend - a.totalCreditsSpend;
        });
    }

    if(limitResults.value){
        results.motions = results.motions.slice(0, 3);
    }


    return results;

});



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
                        <h1>Community voted: ({{ $page.props.election.votes.length }} voters)</h1>
                        <button @click="showStats = !showStats">{{ showStats ? "Hide" : "Show" }} statistics</button>
                        <button @click="sortNetto = !sortNetto">{{ !sortNetto ? "Sort based on votes" : "Sort based on attention" }}</button>
                        <button @click="limitResults = !limitResults">{{ limitResults ? "Show all issues" : "Limit to 3 issues" }}</button>
                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">

                        <h1>My Votes</h1>


                    </div>

                </div>

                <div class="results__result" v-for="motion in sortedResults.motions" :key="motion.motion_uuid"
                    v-if="sortedResults">

                    <!-- {{ motion }} -->
                    <div class="global">
                        <h2>{{ motion.motion_content }} <svg-icon :size="18" type="mdi" :path="mdiThumbUp"
                                v-if="sortedResults.nettoWinner === motion.motion_uuid"
                                v-tooltip="`Highest netto votes`"></svg-icon>
                            <svg-icon :size="18" type="mdi" :path="mdiThumbDown"
                                v-if="sortedResults.nettoLoser === motion.motion_uuid"
                                v-tooltip="`Lowest netto votes`"></svg-icon>
                            <svg-icon :size="18" type="mdi" :path="mdiThumbsUpDown"
                                v-if="sortedResults.mostAttention === motion.motion_uuid"
                                v-tooltip="`Most attention based on credits`"></svg-icon>
                        </h2>



                        <div class="vote-data" v-if="showStats">

                            <div>
                                <span v-tooltip="`Netto votes: in favor minus opposed votes`">{{ motion.nettoVotes }}
                                    Votes</span>
                                <em>{{ motion.totalCreditsSpend }} Total credits spend</em>
                            </div>

                            <div>
                                Total voters: {{ motion.numVoters }}
                            </div>

                            <div>
                                <span v-tooltip="`Total number of votes cast`">Bruto votes: {{ motion.brutoVotes }}</span>
                            </div>

                            <div>
                                <span>In favor: {{ motion.inFavorVotes }}
                                </span>
                                <em>{{ motion.inFavorCredits }} Credits
                                    spent</em>
                            </div>


                            <div>
                                <span>opposed: {{ motion.opposedVotes }}</span>
                                <em>{{ motion.opposedCredits }} Credits
                                    spent</em>

                            </div>

                        </div>

                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">
                        <h2>{{ motion.motion_content }}</h2>


                        <div class="vote-data" v-if="showStats">

<div>
    <span v-tooltip="`Netto votes: in favor minus opposed votes`">{{ UuidVote(motion.motion_uuid)?.votes }} Votes</span>
    <em>{{ UuidVote(motion.motion_uuid)?.credits
                        }} Credits spent</em>
</div>

</div>


                    </div>
                </div>
            </div>
        </div>




    </FrontLayout>
</template>

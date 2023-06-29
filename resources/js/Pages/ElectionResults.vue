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

    let results = {
        motions: [] as VotingTypes.MotionResult[]
    };

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

        results.motions.push(motionResult);

    });

    return results;

};


const UuidVote = (motionUuid: string) => {

    const myUuid = page.props.myVoteUuid;

    let results = ElectionResultsData();

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


const sortedResults = computed(() => {

    let results = ElectionResultsData();

    results.motions.sort((a, b) => {
        return b.nettoVotes - a.nettoVotes;
    });

    return results.motions;

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
                        <h1>Community (n = {{ $page.props.election.votes.length  }}) voted</h1>




                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">

                        <h1>My Votes</h1>


                    </div>

                </div>

                <div class="results__result" v-for="motion in sortedResults" :key="motion.motion_uuid">

                    <!-- {{ motion }} -->
                    <div class="global">
                        <span>Bruto votes: {{ motion.brutoVotes }}</span>
                        <span>Total voters: {{ motion.numVoters }}</span>
                        <h2>{{ motion.motion_content }}</h2>
                        <h3>{{ motion.nettoVotes }} Votes | <em>{{ motion.totalCreditsSpend }} Total credits spend</em></h3>



                        <span>in favor: {{ motion.inFavorVotes }} | <em>{{ motion.inFavorCredits }} Credits
                                spend</em></span>
                        <span>opposed: {{ motion.opposedVotes }} | <em>{{ motion.opposedCredits }} Credits spend</em></span>
                    </div>

                    <div class="personal" v-if="$page.props.myVoteUuid">
                        <h2>{{ motion.motion_content }}</h2>
                        <h3>{{ UuidVote(motion.motion_uuid)?.votes }} Votes | <em>{{ UuidVote(motion.motion_uuid)?.credits
                        }} Credits spend</em>
                        </h3>
                    </div>
                </div>
            </div>
        </div>




    </FrontLayout>
</template>



<style scoped></style>

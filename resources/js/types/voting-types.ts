export interface Motion {
    content: string;
    votes: number;
    uuid: string;
    credits: number;
    visualCredits?: Credit[];
};

export interface MotionResult {

    motion_content: string;
    motion_uuid: string;
    votes: VoteResult[];

    numVoters: number;

    nettoVotes: number;
    brutoVotes: number;

    inFavorVotes: number;
    opposedVotes: number;
    inFavorCredits: number;
    opposedCredits: number;
    totalCreditsSpent: number;
}

export interface ElectionOptions {

    forceSpread: boolean;


}


export interface Election {

    id: number;
    uuid: string;
    name: string;
    description?: string;

    credits: number;
    motions: Motion[];
    options: ElectionOptions;

    votes?: Vote[];

    created_at: string;
    updated_at: string;

}


export interface ElectionResult {

    motions: MotionResult[];
    numVoters: number;
    creditsAvailable: number;
    creditsSpend: number;
    inFavorVotes: number;
    opposedVotes: number;
    inFavorCredits: number;
    opposedCredits: number;
    totalCreditsSpent: number;

    nettoWinner: string | null;
    nettoLoser: string | null;
    mostAttention: string | null;




}




export interface Vote {
    id?: number;
    uuid?: string;
    election_uuid: string;

    name: string;
    remainingCredits: number;
    motions: Motion[];


    created_at?: string;
    updated_at?: string;
}

export interface VoteResult {
    vote_uuid: string;
    votes: number;
    voted_by: string;
    credits: number;
}


export interface Credit {
    creditCode: string;
    targetCode: string;
}

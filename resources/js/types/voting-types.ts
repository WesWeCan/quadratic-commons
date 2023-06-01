export interface Motion {
    content: string;
    votes: number;
    uuid: string;
    credits: number;
};


export interface ElectionOptions {



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




export interface Vote {
    id?: number;
    election_uuid: string;

    name: string;
    remainingCredits: number;
    motions: Motion[];


    created_at?: string;
    updated_at?: string;
}

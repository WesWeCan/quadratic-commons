export interface Motion {
    content: string;
    votes: number;
};

export interface ElectionOptions {



}


export interface Election {

    id: number;
    name: string;
    description?: string;

    credits: number;
    motions: Motion[];
    options: ElectionOptions;

    created_at: string;
    updated_at: string;

}

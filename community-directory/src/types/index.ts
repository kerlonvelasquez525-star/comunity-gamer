export interface Community {
    id: string;
    name: string;
    description: string;
    createdAt: Date;
}

export interface CommunityInput {
    name: string;
    description: string;
}
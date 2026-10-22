export interface Community {
    id: number;
    name: string;
    description: string;
    createdAt: Date;
}

export interface CommunityInput {
    name: string;
    description: string;
}
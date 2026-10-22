import { PrismaClient } from '@prisma/client';
import type { Request, Response } from 'express';
import type { CommunityInput } from '../types';

class CommunityController {
    constructor(private readonly prisma: PrismaClient) {}

    async createCommunity(req: Request, res: Response): Promise<void> {
        const input = this.parseCommunityInput(req.body);

        if (!input) {
            res.status(422).json({ error: 'Name and description are required.' });
            return;
        }

        try {
            const community = await this.prisma.community.create({ data: input });

            res.status(201).json(community);
        } catch (error) {
            this.respondWithServerError(res, 'create community', error);
        }
    }

    async getAllCommunities(_req: Request, res: Response): Promise<void> {
        try {
            const communities = await this.prisma.community.findMany({
                orderBy: { createdAt: 'desc' },
            });

            res.status(200).json(communities);
        } catch (error) {
            this.respondWithServerError(res, 'list communities', error);
        }
    }

    async getCommunityById(req: Request<{ id: string }>, res: Response): Promise<void> {
        const id = this.parseId(req.params.id);

        if (id === null) {
            res.status(400).json({ error: 'Community id must be a positive integer.' });
            return;
        }

        try {
            const community = await this.prisma.community.findUnique({ where: { id } });

            if (!community) {
                res.status(404).json({ error: 'Community not found.' });
                return;
            }

            res.status(200).json(community);
        } catch (error) {
            this.respondWithServerError(res, 'get community', error);
        }
    }

    async updateCommunity(req: Request<{ id: string }>, res: Response): Promise<void> {
        const id = this.parseId(req.params.id);
        const input = this.parseCommunityInput(req.body);

        if (id === null) {
            res.status(400).json({ error: 'Community id must be a positive integer.' });
            return;
        }

        if (!input) {
            res.status(422).json({ error: 'Name and description are required.' });
            return;
        }

        try {
            const existingCommunity = await this.prisma.community.findUnique({ where: { id } });

            if (!existingCommunity) {
                res.status(404).json({ error: 'Community not found.' });
                return;
            }

            const community = await this.prisma.community.update({ where: { id }, data: input });

            res.status(200).json(community);
        } catch (error) {
            this.respondWithServerError(res, 'update community', error);
        }
    }

    async deleteCommunity(req: Request<{ id: string }>, res: Response): Promise<void> {
        const id = this.parseId(req.params.id);

        if (id === null) {
            res.status(400).json({ error: 'Community id must be a positive integer.' });
            return;
        }

        try {
            const existingCommunity = await this.prisma.community.findUnique({ where: { id } });

            if (!existingCommunity) {
                res.status(404).json({ error: 'Community not found.' });
                return;
            }

            await this.prisma.community.delete({ where: { id } });

            res.status(204).send();
        } catch (error) {
            this.respondWithServerError(res, 'delete community', error);
        }
    }

    private parseCommunityInput(value: unknown): CommunityInput | null {
        if (
            typeof value !== 'object'
            || value === null
            || Array.isArray(value)
            || !('name' in value)
            || !('description' in value)
            || typeof value.name !== 'string'
            || typeof value.description !== 'string'
        ) {
            return null;
        }

        const name = value.name.trim();
        const description = value.description.trim();

        return name && description ? { name, description } : null;
    }

    private parseId(value: string): number | null {
        const id = Number(value);

        return Number.isSafeInteger(id) && id > 0 ? id : null;
    }

    private respondWithServerError(res: Response, action: string, error: unknown): void {
        console.error(`Failed to ${action}.`, error);
        res.status(500).json({ error: 'An unexpected error occurred.' });
    }
}

export default CommunityController;

class CommunityController {
    constructor(private communityService: any) {}

    async createCommunity(req: any, res: any) {
        try {
            const communityData = req.body;
            const newCommunity = await this.communityService.create(communityData);
            res.status(201).json(newCommunity);
        } catch (error) {
            res.status(500).json({ error: 'Failed to create community' });
        }
    }

    async getAllCommunities(req: any, res: any) {
        try {
            const communities = await this.communityService.findAll();
            res.status(200).json(communities);
        } catch (error) {
            res.status(500).json({ error: 'Failed to retrieve communities' });
        }
    }

    async getCommunityById(req: any, res: any) {
        try {
            const { id } = req.params;
            const community = await this.communityService.findById(id);
            if (!community) {
                return res.status(404).json({ error: 'Community not found' });
            }
            res.status(200).json(community);
        } catch (error) {
            res.status(500).json({ error: 'Failed to retrieve community' });
        }
    }

    async updateCommunity(req: any, res: any) {
        try {
            const { id } = req.params;
            const communityData = req.body;
            const updatedCommunity = await this.communityService.update(id, communityData);
            if (!updatedCommunity) {
                return res.status(404).json({ error: 'Community not found' });
            }
            res.status(200).json(updatedCommunity);
        } catch (error) {
            res.status(500).json({ error: 'Failed to update community' });
        }
    }

    async deleteCommunity(req: any, res: any) {
        try {
            const { id } = req.params;
            const deletedCommunity = await this.communityService.delete(id);
            if (!deletedCommunity) {
                return res.status(404).json({ error: 'Community not found' });
            }
            res.status(204).send();
        } catch (error) {
            res.status(500).json({ error: 'Failed to delete community' });
        }
    }
}

export default CommunityController;
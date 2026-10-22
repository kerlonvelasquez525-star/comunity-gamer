import { Router } from 'express';
import { PrismaClient } from '@prisma/client';
import CommunityController from '../controllers/communityController';

function communityRoutes(prisma: PrismaClient): Router {
    const router = Router();
    const communityController = new CommunityController(prisma);

    router.post('/', communityController.createCommunity.bind(communityController));
    router.get('/', communityController.getAllCommunities.bind(communityController));
    router.get('/:id', communityController.getCommunityById.bind(communityController));
    router.put('/:id', communityController.updateCommunity.bind(communityController));
    router.delete('/:id', communityController.deleteCommunity.bind(communityController));

    return router;
}

export default communityRoutes;
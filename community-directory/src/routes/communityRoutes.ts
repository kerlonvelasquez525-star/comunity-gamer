import { Router } from 'express';
import CommunityController from '../controllers/communityController';

const router = Router();
const communityController = new CommunityController();

export function setCommunityRoutes(app) {
    app.use('/api/communities', router);

    router.post('/', communityController.createCommunity.bind(communityController));
    router.get('/', communityController.getAllCommunities.bind(communityController));
    router.get('/:id', communityController.getCommunityById.bind(communityController));
    router.put('/:id', communityController.updateCommunity.bind(communityController));
    router.delete('/:id', communityController.deleteCommunity.bind(communityController));
}
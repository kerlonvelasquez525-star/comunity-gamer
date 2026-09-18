import express from 'express';
import { PrismaClient } from '@prisma/client';
import communityRoutes from './routes/communityRoutes';

const app = express();
const prisma = new PrismaClient();

app.use(express.json());

app.use('/api/communities', communityRoutes(prisma));

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});
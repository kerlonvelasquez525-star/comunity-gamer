import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
    const communities = [
        { name: 'Community One', description: 'Description for Community One' },
        { name: 'Community Two', description: 'Description for Community Two' },
        { name: 'Community Three', description: 'Description for Community Three' },
        { name: 'Community Four', description: 'Description for Community Four' },
        { name: 'Community Five', description: 'Description for Community Five' },
        { name: 'Community Six', description: 'Description for Community Six' },
        { name: 'Community Seven', description: 'Description for Community Seven' },
        { name: 'Community Eight', description: 'Description for Community Eight' },
        { name: 'Community Nine', description: 'Description for Community Nine' },
        { name: 'Community Ten', description: 'Description for Community Ten' },
        { name: 'Community Eleven', description: 'Description for Community Eleven' },
        { name: 'Community Twelve', description: 'Description for Community Twelve' },
        { name: 'Community Thirteen', description: 'Description for Community Thirteen' },
        { name: 'Community Fourteen', description: 'Description for Community Fourteen' },
        { name: 'Community Fifteen', description: 'Description for Community Fifteen' },
    ];

    for (const community of communities) {
        await prisma.community.create({
            data: community,
        });
    }

    console.log('Seeding completed: 15 communities created.');
}

main()
    .catch(e => {
        console.error(e);
        process.exit(1);
    })
    .finally(async () => {
        await prisma.$disconnect();
    });
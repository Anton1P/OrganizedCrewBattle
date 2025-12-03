import { NextResponse } from 'next/server';
import { prisma } from '@/lib/db';

export async function GET() {
  try {
    // Check if clans exist
    const count = await prisma.clan.count();
    if (count > 0) {
      return NextResponse.json({ message: 'Database already seeded' });
    }

    await prisma.clan.create({
        data: {
          id: 123456,
          name: 'Les Vainqueurs',
          wins: 10,
          loses: 2,
          elo: 1450,
          eloPeak: 1500,
          top: 1,
          players: {
            create: [
              { id: 111, name: 'Warrior1', },
              { id: 222, name: 'Mage2', },
            ]
          }
        },
      })

    await prisma.clan.create({
        data: {
            id: 654321,
            name: 'Les Challengers',
            wins: 8,
            loses: 4,
            elo: 1380,
            eloPeak: 1400,
            top: 2,
            players: {
            create: [
                { id: 333, name: 'Rogue3', },
            ]
            }
        },
    })

    await prisma.clan.create({
        data: {
            id: 999999,
            name: 'Noobs United',
            wins: 1,
            loses: 10,
            elo: 800,
            eloPeak: 1000,
            top: 3,
        },
    })

    return NextResponse.json({ message: 'Seeding successful' });
  } catch (error) {
    return NextResponse.json({ error: String(error) }, { status: 500 });
  }
}

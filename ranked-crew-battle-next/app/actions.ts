'use server'

import { prisma } from '@/lib/db';

export async function getLeaderboard() {
  try {
    const clans = await prisma.clan.findMany({
      orderBy: {
        elo: 'desc',
      },
      include: {
        players: true,
      },
    });
    return clans;
  } catch (error) {
    console.error("Failed to fetch leaderboard:", error);
    return [];
  }
}

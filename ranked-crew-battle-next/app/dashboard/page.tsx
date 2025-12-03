import { getServerSession } from "next-auth/next";
import { getAuthOptions } from "@/app/api/auth/[...nextauth]/route";
import { prisma } from "@/lib/db";
import { redirect } from "next/navigation";
import MemberView from "./_components/MemberView";
import AdminView from "./_components/AdminView";

export default async function DashboardPage() {
    const session = await getServerSession(getAuthOptions());

    if (!session || !session.user) {
        redirect("/");
    }

    // Since we don't have the real Steam API linking yet, we will TRY to find the player by steamId
    // If we don't find them, we will assume it's a "New User" scenario.

    // FOR TESTING: We will assume the user IS one of our seeded users if their ID matches.
    // In a real app, you would fetch the player by `session.user.steamId`.

    // Temporarily hardcoding retrieval for testing flow until real Steam auth is fully linked
    // Let's try to find a player with the Steam ID from the session (which is fake or real)
    let player = await prisma.player.findFirst({
        where: { steamId: session.user.steamId },
        include: { clan: true }
    });

    // FALLBACK FOR DEMO: If no player found (because we are logging in with a random Steam account
    // that isn't in our seed), let's fake it and attach them to "Les Vainqueurs" as Leader for the demo.
    if (!player) {
         // This block simulates the "Setup" step where we fetch Brawlhalla data
         // Since we can't fetch it, we'll just say: "You are Warrior1 (Leader)"
         player = await prisma.player.findFirst({
             where: { name: 'Warrior1 (Leader)' },
             include: { clan: true }
         });

         // If still nothing (db empty?), showing error
         if (!player) return <div>Database is empty. Please run seed.</div>;
    }

    const isLeaderOrOfficer = player.rank === 'Leader' || player.rank === 'Officer';

    return (
        <div>
            {isLeaderOrOfficer ? (
                <AdminView player={player} />
            ) : (
                <MemberView player={player} />
            )}
        </div>
    );
}

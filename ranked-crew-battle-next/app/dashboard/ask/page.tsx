import { prisma } from "@/lib/db";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";

async function createChallenge(formData: FormData) {
    'use server';

    const requesterId = parseInt(formData.get('requesterId') as string);
    const receiverId = parseInt(formData.get('receiverId') as string);
    const date = formData.get('date') as string;

    // Formats (simplified for now)
    const cbCount = parseInt(formData.get('cb_count') as string || '0');

    if (!date) return;

    await prisma.tournament.create({
        data: {
            requesterId,
            receiverId,
            dateRencontre: new Date(date),
            crewBattleCount: cbCount,
            isAccepted: false,
            isFinished: false
        }
    });

    revalidatePath('/dashboard');
    redirect('/dashboard');
}

export default async function AskPage({ searchParams }: { searchParams: { q?: string } }) {
    // 1. Fetch current user's clan (Hardcoded for demo: Warrior1)
    const me = await prisma.player.findFirst({
        where: { name: 'Warrior1 (Leader)' },
        include: { clan: true }
    });

    if (!me) return <div>User not found</div>;

    // 2. Search for clans
    let clans = [];
    if (searchParams.q) {
        clans = await prisma.clan.findMany({
            where: {
                name: { contains: searchParams.q },
                id: { not: me.clanId } // Don't show my own clan
            }
        });
    } else {
        // Show some suggestions
         clans = await prisma.clan.findMany({
            where: { id: { not: me.clanId }},
            take: 5
        });
    }

    return (
        <div className="max-w-2xl mx-auto space-y-8">
            <div>
                <h1 className="text-3xl font-bold">Challenge a Clan</h1>
                <p className="text-gray-400">Search for a clan to start a Crew Battle.</p>
            </div>

            {/* Search Form */}
            <form className="flex gap-2">
                <input
                    type="text"
                    name="q"
                    placeholder="Search clan name..."
                    defaultValue={searchParams.q}
                    className="flex-1 bg-gray-800 border border-gray-700 rounded px-4 py-2 text-white"
                />
                <button className="bg-blue-600 px-6 py-2 rounded font-semibold">Search</button>
            </form>

            {/* Results */}
            <div className="space-y-4">
                {clans.map(clan => (
                    <div key={clan.id} className="bg-gray-800 p-4 rounded border border-gray-700 flex justify-between items-center">
                        <div>
                            <div className="font-bold text-lg">{clan.name}</div>
                            <div className="text-sm text-yellow-500 font-mono">Elo: {clan.elo}</div>
                        </div>

                        {/* Challenge Form Modal/Inline */}
                        <form action={createChallenge} className="flex items-center gap-2">
                            <input type="hidden" name="requesterId" value={me.clanId} />
                            <input type="hidden" name="receiverId" value={clan.id} />

                            <input
                                type="datetime-local"
                                name="date"
                                required
                                className="bg-gray-900 border border-gray-600 rounded px-2 py-1 text-sm text-white"
                            />

                            <input type="hidden" name="cb_count" value="1" />

                            <button className="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-1.5 rounded text-sm font-semibold transition">
                                Challenge
                            </button>
                        </form>
                    </div>
                ))}

                {clans.length === 0 && (
                    <div className="text-center text-gray-500 py-8">
                        No clans found.
                    </div>
                )}
            </div>
        </div>
    );
}

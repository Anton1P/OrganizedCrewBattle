import { prisma } from "@/lib/db";
import { calculateElo } from "@/lib/elo";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";

async function submitReport(formData: FormData) {
    'use server';

    const tournamentId = parseInt(formData.get('tournamentId') as string);
    const winnerId = parseInt(formData.get('winnerId') as string);
    const loserId = parseInt(formData.get('loserId') as string);

    // 1. Get current Elo
    const winnerClan = await prisma.clan.findUnique({ where: { id: winnerId }});
    const loserClan = await prisma.clan.findUnique({ where: { id: loserId }});

    if (!winnerClan || !loserClan) return;

    // 2. Calculate new Elo
    const [newWinnerElo, newLoserElo] = calculateElo(winnerClan.elo, loserClan.elo);

    // 3. Update Clans
    await prisma.clan.update({
        where: { id: winnerId },
        data: {
            elo: newWinnerElo,
            wins: { increment: 1 },
            eloPeak: Math.max(winnerClan.eloPeak, newWinnerElo)
        }
    });

    await prisma.clan.update({
        where: { id: loserId },
        data: {
            elo: newLoserElo,
            loses: { increment: 1 }
        }
    });

    // 4. Update Tournament
    await prisma.tournament.update({
        where: { id: tournamentId },
        data: {
            isFinished: true,
            winnerId: winnerId,
            loserId: loserId
        }
    });

    // 5. Update Ranks (Top) - simplified
    // In a real app, you might want to recalculate all ranks or do this in a background job
    const allClans = await prisma.clan.findMany({ orderBy: { elo: 'desc' } });
    for (let i = 0; i < allClans.length; i++) {
        await prisma.clan.update({
            where: { id: allClans[i].id },
            data: { top: i + 1 }
        });
    }

    revalidatePath('/dashboard');
    redirect('/dashboard');
}

export default async function ReportPage() {
    // 1. Fetch current user (Hardcoded: Warrior1)
    const me = await prisma.player.findFirst({
        where: { name: 'Warrior1 (Leader)' },
        include: { clan: true }
    });

    if (!me) return <div>User not found</div>;

    // 2. Find pending accepted matches for this clan
    const matches = await prisma.tournament.findMany({
        where: {
            OR: [
                { requesterId: me.clanId },
                { receiverId: me.clanId }
            ],
            isAccepted: true,
            isFinished: false
        },
        include: {
            requester: true,
            receiver: true
        }
    });

    return (
        <div className="max-w-2xl mx-auto space-y-8">
            <div>
                <h1 className="text-3xl font-bold">Report Match Result</h1>
                <p className="text-gray-400">Declare the winner to update the rankings.</p>
            </div>

            <div className="space-y-4">
                {matches.length === 0 ? (
                    <div className="text-center bg-gray-800 p-8 rounded border border-gray-700 text-gray-400">
                        No active matches found to report.
                    </div>
                ) : (
                    matches.map(match => {
                        const opponent = match.requesterId === me.clanId ? match.receiver : match.requester;
                        const myClan = match.requesterId === me.clanId ? match.requester : match.receiver;

                        return (
                            <div key={match.id} className="bg-gray-800 p-6 rounded border border-gray-700">
                                <div className="flex justify-between items-center mb-6">
                                    <div className="text-xl font-bold text-white">{myClan.name}</div>
                                    <div className="text-gray-500 font-mono">VS</div>
                                    <div className="text-xl font-bold text-white">{opponent.name}</div>
                                </div>

                                <div className="grid grid-cols-2 gap-4">
                                    <form action={submitReport} className="w-full">
                                        <input type="hidden" name="tournamentId" value={match.id} />
                                        <input type="hidden" name="winnerId" value={myClan.id} />
                                        <input type="hidden" name="loserId" value={opponent.id} />
                                        <button className="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded font-bold uppercase tracking-wider transition">
                                            We Won
                                        </button>
                                    </form>

                                    <form action={submitReport} className="w-full">
                                        <input type="hidden" name="tournamentId" value={match.id} />
                                        <input type="hidden" name="winnerId" value={opponent.id} />
                                        <input type="hidden" name="loserId" value={myClan.id} />
                                        <button className="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded font-bold uppercase tracking-wider transition">
                                            We Lost
                                        </button>
                                    </form>
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </div>
    );
}

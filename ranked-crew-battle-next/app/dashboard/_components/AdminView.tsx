import { prisma } from "@/lib/db";
import { revalidatePath } from "next/cache";

async function getTournaments(clanId: number) {
    // Received (pending)
    const received = await prisma.tournament.findMany({
        where: { receiverId: clanId, isAccepted: false, isFinished: false },
        include: { requester: true }
    });

    // Demanded (sent)
    const sent = await prisma.tournament.findMany({
        where: { requesterId: clanId, isFinished: false },
        include: { receiver: true }
    });

    return { received, sent };
}

export default async function AdminView({ player }: { player: any }) {
    const { received, sent } = await getTournaments(player.clanId);

    return (
        <div className="space-y-8">
            <div className="flex justify-between items-end">
                <div>
                    <h1 className="text-3xl font-bold">Admin Dashboard</h1>
                    <p className="text-gray-400">Managing {player.clan.name}</p>
                </div>
                <div className="bg-yellow-900/30 border border-yellow-700/50 px-4 py-2 rounded text-yellow-500 text-sm font-mono">
                    Elo: {player.clan.elo}
                </div>
            </div>

            {/* Notifications Section */}
            <div className="grid md:grid-cols-2 gap-6">

                {/* RECEIVED REQUESTS */}
                <div className="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-lg relative overflow-hidden">
                    <div className="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
                        <span className="bg-blue-500/20 text-blue-400 p-1.5 rounded-md text-sm">INBOX</span>
                        Received Battles
                    </h2>

                    {received.length === 0 ? (
                        <p className="text-gray-500 italic text-sm">No pending requests.</p>
                    ) : (
                        <ul className="space-y-3">
                            {received.map((t) => (
                                <li key={t.id} className="bg-gray-900/50 p-3 rounded border border-gray-700 flex justify-between items-center">
                                    <div>
                                        <div className="font-bold text-white">{t.requester.name}</div>
                                        <div className="text-xs text-gray-400">
                                            {new Date(t.dateRencontre).toLocaleDateString()}
                                        </div>
                                    </div>
                                    <div className="flex gap-2">
                                        <form action={async () => {
                                            'use server';
                                            await prisma.tournament.update({ where: { id: t.id }, data: { isAccepted: true }});
                                            revalidatePath('/dashboard');
                                        }}>
                                            <button className="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">Accept</button>
                                        </form>

                                        <form action={async () => {
                                            'use server';
                                            await prisma.tournament.delete({ where: { id: t.id }});
                                            revalidatePath('/dashboard');
                                        }}>
                                            <button className="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">Deny</button>
                                        </form>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>

                {/* SENT REQUESTS */}
                <div className="bg-gray-800 p-6 rounded-lg border border-gray-700 shadow-lg relative overflow-hidden">
                    <div className="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                     <h2 className="text-xl font-semibold mb-4 flex items-center gap-2">
                        <span className="bg-purple-500/20 text-purple-400 p-1.5 rounded-md text-sm">OUTBOX</span>
                        Requested Battles
                    </h2>

                    {sent.length === 0 ? (
                        <p className="text-gray-500 italic text-sm">You haven't challenged anyone yet.</p>
                    ) : (
                        <ul className="space-y-3">
                            {sent.map((t) => (
                                <li key={t.id} className="bg-gray-900/50 p-3 rounded border border-gray-700 flex justify-between items-center">
                                    <div>
                                        <div className="font-bold text-white">vs {t.receiver.name}</div>
                                        <div className="text-xs text-gray-400">
                                            {new Date(t.dateRencontre).toLocaleDateString()}
                                        </div>
                                    </div>
                                    <span className={`text-xs px-2 py-1 rounded ${t.isAccepted ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-400'}`}>
                                        {t.isAccepted ? 'Accepted' : 'Pending'}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    )}

                    <div className="mt-6 pt-4 border-t border-gray-700">
                        <a href="/dashboard/ask" className="block w-full text-center bg-gray-700 hover:bg-gray-600 text-white py-2 rounded transition text-sm">
                            + Challenge a Clan
                        </a>
                    </div>
                </div>
            </div>

            {/* ACTIONS */}
            <div className="grid md:grid-cols-2 gap-6">
                 <div className="bg-gray-800 p-6 rounded-lg border border-gray-700">
                    <h3 className="font-semibold text-lg mb-4">Actions</h3>
                    <div className="flex gap-4">
                         <a href="/dashboard/report" className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm transition">
                            Report Match Result
                        </a>
                    </div>
                 </div>
            </div>
        </div>
    );
}

export default function MemberView({ player }: { player: any }) {
    return (
        <div className="space-y-6">
            <h1 className="text-3xl font-bold">Welcome, {player.name}</h1>
            <div className="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <h2 className="text-xl font-semibold mb-4 text-blue-400">Clan Status</h2>
                <p>Clan: <span className="font-bold">{player.clan.name}</span></p>
                <p>Role: <span className="text-gray-400">{player.rank}</span></p>

                <div className="mt-4 grid grid-cols-2 gap-4">
                    <div className="bg-gray-900 p-4 rounded text-center">
                        <div className="text-2xl font-bold text-green-500">{player.clan.wins}</div>
                        <div className="text-xs text-gray-500 uppercase">Wins</div>
                    </div>
                    <div className="bg-gray-900 p-4 rounded text-center">
                        <div className="text-2xl font-bold text-red-500">{player.clan.loses}</div>
                        <div className="text-xs text-gray-500 uppercase">Losses</div>
                    </div>
                </div>
            </div>

            <div className="bg-gray-800 p-6 rounded-lg border border-gray-700 opacity-50">
                <h2 className="text-xl font-semibold mb-2">Upcoming Matches</h2>
                <p className="text-gray-400 text-sm">You do not have permission to manage matches.</p>
            </div>
        </div>
    );
}

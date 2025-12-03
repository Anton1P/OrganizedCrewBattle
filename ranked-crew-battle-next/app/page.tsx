import { getLeaderboard } from "./actions";

export default async function Home() {
  const clans = await getLeaderboard();

  return (
    <main className="min-h-screen bg-gray-900 text-white p-8">
      <div className="max-w-4xl mx-auto">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-4xl font-bold text-yellow-500">Ranked Crew Battle</h1>
          {/* Login button placeholder */}
          <div className="text-sm text-gray-400">Not logged in</div>
        </div>

        <div className="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
          <div className="p-6 border-b border-gray-700">
            <h2 className="text-2xl font-semibold">Leaderboard</h2>
            <p className="text-gray-400">Top clans sorted by Elo rating</p>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead className="bg-gray-700 text-gray-200">
                <tr>
                  <th className="px-6 py-4 font-medium">Rank</th>
                  <th className="px-6 py-4 font-medium">Clan</th>
                  <th className="px-6 py-4 font-medium text-center">Elo</th>
                  <th className="px-6 py-4 font-medium text-center">W / L</th>
                  <th className="px-6 py-4 font-medium text-center">Peak</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-700">
                {clans.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="px-6 py-8 text-center text-gray-500">
                      No clans found.
                    </td>
                  </tr>
                ) : (
                  clans.map((clan, index) => (
                    <tr key={clan.id} className="hover:bg-gray-750 transition-colors">
                      <td className="px-6 py-4 text-xl font-bold text-gray-400">
                        #{index + 1}
                      </td>
                      <td className="px-6 py-4">
                        <div className="font-semibold text-lg">{clan.name}</div>
                        <div className="text-sm text-gray-500">{clan.players.length} members</div>
                      </td>
                      <td className="px-6 py-4 text-center font-mono text-yellow-400 font-bold">
                        {clan.elo}
                      </td>
                      <td className="px-6 py-4 text-center">
                        <span className="text-green-400 font-bold">{clan.wins}</span>
                        <span className="text-gray-500 mx-1">/</span>
                        <span className="text-red-400 font-bold">{clan.loses}</span>
                      </td>
                      <td className="px-6 py-4 text-center text-gray-500">
                        {clan.eloPeak}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  );
}

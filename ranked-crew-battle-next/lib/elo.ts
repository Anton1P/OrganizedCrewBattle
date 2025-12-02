/**
 * Calculates the new Elo ratings for a winner and a loser.
 * Based on the original PHP implementation.
 *
 * @param eloWinner The current Elo rating of the winner.
 * @param eloLoser The current Elo rating of the loser.
 * @param kFactor The K-factor determines how much the rating changes (default 30).
 * @returns An array containing [newEloWinner, newEloLoser].
 */
export function calculateElo(eloWinner: number, eloLoser: number, kFactor: number = 30): [number, number] {
    const expectedWinner = 1 / (1 + Math.pow(10, (eloLoser - eloWinner) / 400));
    const expectedLoser = 1 - expectedWinner;

    const newEloWinner = Math.round(eloWinner + kFactor * (1 - expectedWinner));
    const newEloLoser = Math.round(eloLoser + kFactor * (0 - expectedLoser));

    return [newEloWinner, newEloLoser];
}

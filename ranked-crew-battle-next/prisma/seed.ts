import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

async function main() {
  console.log('Seeding database...')

  // Create Clans
  const clanA = await prisma.clan.create({
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
          { id: 111, name: 'Warrior1' },
          { id: 222, name: 'Mage2' },
        ]
      }
    },
  })

  const clanB = await prisma.clan.create({
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
          { id: 333, name: 'Rogue3' },
        ]
      }
    },
  })

  const clanC = await prisma.clan.create({
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

  console.log('Database seeded!')
}

main()
  .then(async () => {
    await prisma.$disconnect()
  })
  .catch(async (e) => {
    console.error(e)
    await prisma.$disconnect()
    process.exit(1)
  })

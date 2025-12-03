import NextAuth, { NextAuthOptions } from "next-auth";
import { PrismaAdapter } from "@next-auth/prisma-adapter";
import SteamProvider from "next-auth-steam";
import type { NextRequest } from "next/server";
import { prisma } from "@/lib/db";

export function getAuthOptions(req?: NextRequest | Request): NextAuthOptions {
  return {
    adapter: PrismaAdapter(prisma),
    providers: [
      SteamProvider(req as any, {
        clientSecret: process.env.STEAM_API_KEY!,
        callbackUrl: `${process.env.NEXTAUTH_URL}/api/auth/callback`,
      }),
    ],
    secret: process.env.NEXTAUTH_SECRET,
    callbacks: {
      async session({ session, user }) {
        if (session.user) {
          session.user.steamId = (user as any).steamId;
          session.user.id = user.id;
        }
        return session;
      },
    },
  };
}

async function handler(
  req: NextRequest,
  props: { params: Promise<{ nextauth: string[] }> }
) {
  // Await params as required by Next.js 15+
  const params = await props.params;
  return NextAuth(req, { params }, getAuthOptions(req));
}

export { handler as GET, handler as POST };

'use client';

import { useSession, signOut } from "next-auth/react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect } from "react";

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { data: session, status } = useSession();
  const router = useRouter();

  useEffect(() => {
    if (status === 'unauthenticated') {
      router.push('/');
    }
  }, [status, router]);

  if (status === 'loading') {
      return <div className="min-h-screen bg-gray-900 text-white flex items-center justify-center">Loading...</div>;
  }

  if (!session) {
      return null;
  }

  return (
    <div className="min-h-screen bg-gray-900 text-white font-sans">
      <nav className="bg-gray-800 border-b border-gray-700 p-4">
        <div className="max-w-6xl mx-auto flex justify-between items-center">
          <Link href="/dashboard" className="text-xl font-bold text-yellow-500 hover:text-yellow-400">
            Ranked Crew Battle
          </Link>

          <div className="flex items-center gap-4">
             <div className="flex items-center gap-3">
                {session.user?.image && (
                    <img
                        src={session.user.image}
                        alt={session.user.name || "User"}
                        className="w-8 h-8 rounded-full border border-gray-600"
                    />
                )}
                <span className="text-sm text-gray-300">
                    {session.user?.name}
                </span>
             </div>

             <button
                onClick={() => signOut({ callbackUrl: '/' })}
                className="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded transition"
             >
                Logout
             </button>
          </div>
        </div>
      </nav>

      <main className="max-w-6xl mx-auto p-6">
        {children}
      </main>
    </div>
  );
}

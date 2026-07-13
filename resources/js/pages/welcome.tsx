import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, BookOpenCheck, Clock3, GraduationCap, ShieldCheck, Sparkles } from 'lucide-react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Learn Laravel 13" />

            <main className="min-h-screen bg-zinc-950 text-white">
                <div className="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 py-6">
                    <header className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="flex size-10 items-center justify-center rounded-md bg-emerald-400 text-zinc-950">
                                <Sparkles className="size-5" />
                            </div>
                            <span className="text-lg font-semibold">LL13</span>
                        </div>

                        <nav className="flex items-center gap-3">
                            {auth.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="inline-flex items-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-medium text-zinc-950 hover:bg-zinc-200"
                                >
                                    Dashboard
                                    <ArrowRight className="size-4" />
                                </Link>
                            ) : (
                                <>
                                    <Link href={route('login')} className="rounded-md px-4 py-2 text-sm font-medium text-zinc-300 hover:text-white">
                                        Log in
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="rounded-md bg-white px-4 py-2 text-sm font-medium text-zinc-950 hover:bg-zinc-200"
                                    >
                                        Register
                                    </Link>
                                </>
                            )}
                        </nav>
                    </header>

                    <section className="grid flex-1 items-center gap-10 py-12 lg:grid-cols-[1fr_420px]">
                        <div className="max-w-2xl">
                            <div className="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-300/30 bg-emerald-300/10 px-3 py-1 text-sm text-emerald-100">
                                <ShieldCheck className="size-4" />
                                Laravel 13.19.0 · PHP 8.4
                            </div>

                            <h1 className="text-5xl leading-tight font-semibold tracking-normal md:text-7xl">Build skill with visible progress.</h1>

                            <p className="mt-6 max-w-xl text-lg leading-8 text-zinc-300">
                                Learn Laravel 13 through guided quests, MCQs, hands-on tasks, study logs, and visible weekly progress.
                            </p>

                            <div className="mt-8 flex flex-wrap gap-3">
                                <Link
                                    href={auth.user ? route('dashboard') : route('register')}
                                    className="inline-flex items-center gap-2 rounded-md bg-emerald-400 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-emerald-300"
                                >
                                    Open LL13
                                    <ArrowRight className="size-4" />
                                </Link>
                                <a
                                    href="https://laravel.com/docs/13.x"
                                    className="inline-flex items-center gap-2 rounded-md border border-white/15 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10"
                                >
                                    Laravel 13 docs
                                </a>
                            </div>
                        </div>

                        <div className="rounded-lg border border-white/10 bg-white/[0.04] p-5 shadow-2xl">
                            <div className="space-y-4">
                                <PreviewItem icon={GraduationCap} label="Learning path" value="Laravel 13 fundamentals" accent="bg-emerald-400" />
                                <PreviewItem icon={Clock3} label="Logged this week" value="3h 20m" accent="bg-sky-400" />
                                <PreviewItem icon={BookOpenCheck} label="Current focus" value="Policies, migrations, Inertia" accent="bg-amber-300" />
                            </div>

                            <div className="mt-6 rounded-md bg-zinc-900 p-4">
                                <div className="flex items-center justify-between text-sm">
                                    <span className="font-medium">Weekly progress</span>
                                    <span className="text-emerald-300">83%</span>
                                </div>
                                <div className="mt-3 h-2 overflow-hidden rounded-full bg-zinc-800">
                                    <div className="h-full w-[83%] rounded-full bg-emerald-400" />
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </>
    );
}

function PreviewItem({ icon: Icon, label, value, accent }: { icon: typeof GraduationCap; label: string; value: string; accent: string }) {
    return (
        <div className="flex items-center gap-4 rounded-md bg-zinc-900 p-4">
            <div className={`flex size-10 items-center justify-center rounded-md text-zinc-950 ${accent}`}>
                <Icon className="size-5" />
            </div>
            <div>
                <p className="text-sm text-zinc-400">{label}</p>
                <p className="mt-1 font-medium">{value}</p>
            </div>
        </div>
    );
}

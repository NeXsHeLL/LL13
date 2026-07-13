import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import {
    BookOpenCheck,
    CalendarClock,
    CheckCircle2,
    Clock3,
    Flame,
    GraduationCap,
    Plus,
    Rocket,
    Target,
    Trash2,
    Trophy,
    type LucideIcon,
} from 'lucide-react';
import { type FormEvent, type ReactNode } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type LearningLog = {
    id: number;
    learned_on: string;
    minutes: number;
    topic: string;
    reflection: string;
};

type LearningCheckpoint = {
    id: number;
    title: string;
    notes: string | null;
    activity_type: 'task' | 'mcq';
    difficulty: 'basics' | 'intermediate' | 'advanced' | string;
    prompt: string | null;
    options: { key: string; text: string }[] | null;
    user_answer: string | null;
    explanation: string | null;
    is_complete: boolean;
};

type LearningPath = {
    id: number;
    title: string;
    focus_area: string;
    outcome: string;
    status: 'planned' | 'active' | 'paused' | 'completed';
    weekly_minutes: number;
    confidence: number;
    target_date: string | null;
    logged_minutes: number;
    checkpoints_count: number;
    completed_checkpoints_count: number;
    created_at: string;
    checkpoints: LearningCheckpoint[];
    logs: LearningLog[];
};

type Stats = {
    active_paths: number;
    total_minutes: number;
    planned_minutes: number;
    average_confidence: number;
    completed_checkpoints: number;
};

type QuestCatalogItem = {
    id: string;
    title: string;
    level: string;
    description: string;
    source_label: string;
    source_url: string;
    activity_count: number;
};

type DashboardProps = {
    paths: LearningPath[];
    stats: Stats;
    questCatalog: QuestCatalogItem[];
};

const statuses = ['planned', 'active', 'paused', 'completed'] as const;

export default function Dashboard({ paths, stats, questCatalog }: DashboardProps) {
    const initialQuest = questCatalog[0]?.id ?? 'laravel-full-stack';

    const pathForm = useForm({
        title: '',
        focus_area: '',
        outcome: '',
        status: 'active',
        weekly_minutes: 180,
        confidence: 3,
        target_date: '',
    });

    const questForm = useForm({
        quest: initialQuest,
    });

    const selectedQuest = questCatalog.find((quest) => quest.id === questForm.data.quest) ?? questCatalog[0];

    function createPath(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        pathForm.post(route('learning-paths.store'), {
            preserveScroll: true,
            onSuccess: () => pathForm.reset(),
        });
    }

    function createQuest() {
        questForm.post(route('learning-paths.quest.store'), {
            preserveScroll: true,
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="LL13 Dashboard" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <section className="grid gap-3 md:grid-cols-5">
                    <StatCard icon={Flame} label="Active paths" value={stats.active_paths.toString()} />
                    <StatCard icon={Clock3} label="Logged hours" value={(stats.total_minutes / 60).toFixed(1)} />
                    <StatCard icon={CheckCircle2} label="Checkpoints" value={stats.completed_checkpoints.toString()} />
                    <StatCard icon={CalendarClock} label="Weekly plan" value={`${stats.planned_minutes}m`} />
                    <StatCard icon={Target} label="Confidence" value={stats.average_confidence ? `${stats.average_confidence}/5` : '0/5'} />
                </section>

                <section className="grid gap-6 xl:grid-cols-[minmax(300px,380px)_1fr]">
                    <div className="space-y-4">
                        <Card className="border-emerald-500/30 bg-emerald-500/5">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <Rocket className="size-5 text-emerald-500" />
                                    Launch guided quest
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <Field label="Quest" error={questForm.errors.quest}>
                                    <Select value={questForm.data.quest} onValueChange={(value) => questForm.setData('quest', value)}>
                                        <SelectTrigger>
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {questCatalog.map((quest) => (
                                                <SelectItem key={quest.id} value={quest.id}>
                                                    {quest.title}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </Field>

                                <div className="bg-background/60 rounded-md border p-4">
                                    {selectedQuest ? (
                                        <>
                                            <div className="flex flex-wrap items-center gap-2 text-sm font-medium">
                                                <Trophy className="size-4 text-amber-500" />
                                                <span>{selectedQuest.title}</span>
                                                <Badge variant="outline">{selectedQuest.level}</Badge>
                                                <Badge variant="secondary">{selectedQuest.activity_count} activities</Badge>
                                            </div>
                                            <p className="text-muted-foreground mt-2 text-sm">{selectedQuest.description}</p>
                                            <a
                                                className="text-muted-foreground hover:text-foreground mt-3 inline-flex text-xs underline underline-offset-4"
                                                href={selectedQuest.source_url}
                                                target="_blank"
                                                rel="noreferrer"
                                            >
                                                Inspired by {selectedQuest.source_label}
                                            </a>
                                        </>
                                    ) : (
                                        <p className="text-muted-foreground text-sm">No guided quests are available yet.</p>
                                    )}
                                </div>
                                <Button className="w-full" onClick={createQuest} disabled={questForm.processing || !selectedQuest}>
                                    <Rocket />
                                    Generate quest
                                </Button>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <GraduationCap className="size-5" />
                                    Custom learning path
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <form className="space-y-4" onSubmit={createPath}>
                                    <Field label="Title" error={pathForm.errors.title}>
                                        <Input value={pathForm.data.title} onChange={(event) => pathForm.setData('title', event.target.value)} />
                                    </Field>

                                    <Field label="Focus area" error={pathForm.errors.focus_area}>
                                        <Input
                                            value={pathForm.data.focus_area}
                                            onChange={(event) => pathForm.setData('focus_area', event.target.value)}
                                        />
                                    </Field>

                                    <Field label="Outcome" error={pathForm.errors.outcome}>
                                        <textarea
                                            className="border-input bg-background ring-offset-background focus-visible:ring-ring min-h-24 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden"
                                            value={pathForm.data.outcome}
                                            onChange={(event) => pathForm.setData('outcome', event.target.value)}
                                        />
                                    </Field>

                                    <div className="grid gap-3 sm:grid-cols-3">
                                        <Field label="Status" error={pathForm.errors.status}>
                                            <Select value={pathForm.data.status} onValueChange={(value) => pathForm.setData('status', value)}>
                                                <SelectTrigger>
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {statuses.map((status) => (
                                                        <SelectItem key={status} value={status}>
                                                            {status}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </Field>

                                        <Field label="Minutes" error={pathForm.errors.weekly_minutes}>
                                            <Input
                                                type="number"
                                                min="15"
                                                max="3000"
                                                value={pathForm.data.weekly_minutes}
                                                onChange={(event) => pathForm.setData('weekly_minutes', Number(event.target.value))}
                                            />
                                        </Field>

                                        <Field label="Confidence" error={pathForm.errors.confidence}>
                                            <Input
                                                type="number"
                                                min="1"
                                                max="5"
                                                value={pathForm.data.confidence}
                                                onChange={(event) => pathForm.setData('confidence', Number(event.target.value))}
                                            />
                                        </Field>
                                    </div>

                                    <Field label="Target date" error={pathForm.errors.target_date}>
                                        <Input
                                            type="date"
                                            value={pathForm.data.target_date}
                                            onChange={(event) => pathForm.setData('target_date', event.target.value)}
                                        />
                                    </Field>

                                    <Button className="w-full" disabled={pathForm.processing}>
                                        <Plus />
                                        Add path
                                    </Button>
                                </form>
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-4">
                        {paths.length === 0 ? (
                            <div className="bg-muted/20 flex min-h-72 items-center justify-center rounded-lg border border-dashed p-8 text-center">
                                <div className="max-w-sm space-y-3">
                                    <BookOpenCheck className="text-muted-foreground mx-auto size-10" />
                                    <h2 className="text-lg font-semibold">Start with one focused path</h2>
                                    <p className="text-muted-foreground text-sm">Create a path for the topic you want to practice first.</p>
                                </div>
                            </div>
                        ) : (
                            paths.map((path) => <LearningPathCard key={path.id} path={path} />)
                        )}
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}

function LearningPathCard({ path }: { path: LearningPath }) {
    const statusForm = useForm({
        title: path.title,
        focus_area: path.focus_area,
        outcome: path.outcome,
        status: path.status,
        weekly_minutes: path.weekly_minutes,
        confidence: path.confidence,
        target_date: path.target_date ?? '',
    });

    const logForm = useForm({
        learned_on: new Date().toISOString().slice(0, 10),
        minutes: 45,
        topic: '',
        reflection: '',
    });

    const checkpointForm = useForm({
        title: '',
        notes: '',
    });

    const progress = Math.min(100, Math.round((path.logged_minutes / Math.max(path.weekly_minutes, 1)) * 100));
    const checkpointProgress = path.checkpoints_count ? Math.round((path.completed_checkpoints_count / path.checkpoints_count) * 100) : 0;

    function updateStatus(value: string) {
        statusForm.setData('status', value);
        router.put(
            route('learning-paths.update', path.id),
            {
                ...statusForm.data,
                status: value,
            },
            { preserveScroll: true },
        );
    }

    function createLog(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        logForm.post(route('learning-paths.logs.store', path.id), {
            preserveScroll: true,
            onSuccess: () => logForm.reset('topic', 'reflection'),
        });
    }

    function createCheckpoint(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        checkpointForm.post(route('learning-paths.checkpoints.store', path.id), {
            preserveScroll: true,
            onSuccess: () => checkpointForm.reset(),
        });
    }

    function toggleCheckpoint(checkpoint: LearningCheckpoint, isComplete: boolean) {
        router.put(
            route('learning-paths.checkpoints.update', [path.id, checkpoint.id]),
            {
                is_complete: isComplete,
            },
            { preserveScroll: true },
        );
    }

    function deleteCheckpoint(checkpoint: LearningCheckpoint) {
        router.delete(route('learning-paths.checkpoints.destroy', [path.id, checkpoint.id]), { preserveScroll: true });
    }

    function deletePath() {
        router.delete(route('learning-paths.destroy', path.id), { preserveScroll: true });
    }

    return (
        <Card>
            <CardHeader className="gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div className="space-y-2">
                    <div className="flex flex-wrap items-center gap-2">
                        <CardTitle className="text-xl">{path.title}</CardTitle>
                        <Badge variant={path.status === 'active' ? 'default' : 'secondary'}>{path.status}</Badge>
                    </div>
                    <p className="text-muted-foreground text-sm">{path.outcome}</p>
                </div>
                <Button type="button" variant="ghost" size="icon" onClick={deletePath} aria-label={`Delete ${path.title}`}>
                    <Trash2 />
                </Button>
            </CardHeader>

            <CardContent className="space-y-5">
                <div className="grid gap-3 sm:grid-cols-4">
                    <Metric label="Focus" value={path.focus_area} />
                    <Metric label="Target" value={path.target_date ?? 'Open'} />
                    <Metric label="Logged" value={`${path.logged_minutes}m`} />
                    <Metric label="Milestones" value={`${path.completed_checkpoints_count}/${path.checkpoints_count}`} />
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="font-medium">Weekly minutes</span>
                        <span className="text-muted-foreground">{progress}%</span>
                    </div>
                    <div className="bg-muted h-2 overflow-hidden rounded-full md:col-start-1">
                        <div className="h-full rounded-full bg-emerald-600 transition-all" style={{ width: `${progress}%` }} />
                    </div>
                    <div className="flex items-center justify-between text-sm md:col-start-2 md:row-start-1">
                        <span className="font-medium">Checkpoint progress</span>
                        <span className="text-muted-foreground">{checkpointProgress}%</span>
                    </div>
                    <div className="bg-muted h-2 overflow-hidden rounded-full md:col-start-2">
                        <div className="h-full rounded-full bg-sky-600 transition-all" style={{ width: `${checkpointProgress}%` }} />
                    </div>
                </div>

                <div className="rounded-md border p-4">
                    <div className="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h3 className="text-sm font-semibold">Checkpoints</h3>
                            <p className="text-muted-foreground text-sm">Break the path into proof-of-learning milestones.</p>
                        </div>
                        <Badge variant="outline">{path.checkpoints_count}</Badge>
                    </div>

                    <form className="grid gap-3 md:grid-cols-[1fr_1fr_auto]" onSubmit={createCheckpoint}>
                        <Field label="Task" error={checkpointForm.errors.title}>
                            <Input value={checkpointForm.data.title} onChange={(event) => checkpointForm.setData('title', event.target.value)} />
                        </Field>
                        <Field label="Notes" error={checkpointForm.errors.notes}>
                            <Input value={checkpointForm.data.notes} onChange={(event) => checkpointForm.setData('notes', event.target.value)} />
                        </Field>
                        <Button className="self-end" disabled={checkpointForm.processing}>
                            <Plus />
                            Add
                        </Button>
                    </form>

                    {path.checkpoints.length > 0 && (
                        <div className="mt-4 grid gap-2">
                            {path.checkpoints.map((checkpoint) => (
                                <CheckpointActivity
                                    key={checkpoint.id}
                                    checkpoint={checkpoint}
                                    pathId={path.id}
                                    onToggle={toggleCheckpoint}
                                    onDelete={deleteCheckpoint}
                                />
                            ))}
                        </div>
                    )}
                </div>

                <div className="grid gap-4 lg:grid-cols-[180px_1fr]">
                    <Field label="Status" error={statusForm.errors.status}>
                        <Select value={statusForm.data.status} onValueChange={updateStatus} disabled={statusForm.processing}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {statuses.map((status) => (
                                    <SelectItem key={status} value={status}>
                                        {status}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </Field>

                    <form className="grid gap-3 md:grid-cols-[140px_120px_1fr] xl:grid-cols-[150px_120px_1fr_1fr_auto]" onSubmit={createLog}>
                        <Field label="Date" error={logForm.errors.learned_on}>
                            <Input
                                type="date"
                                value={logForm.data.learned_on}
                                onChange={(event) => logForm.setData('learned_on', event.target.value)}
                            />
                        </Field>
                        <Field label="Minutes" error={logForm.errors.minutes}>
                            <Input
                                type="number"
                                min="5"
                                max="720"
                                value={logForm.data.minutes}
                                onChange={(event) => logForm.setData('minutes', Number(event.target.value))}
                            />
                        </Field>
                        <Field label="Topic" error={logForm.errors.topic}>
                            <Input value={logForm.data.topic} onChange={(event) => logForm.setData('topic', event.target.value)} />
                        </Field>
                        <Field label="Reflection" error={logForm.errors.reflection}>
                            <Input value={logForm.data.reflection} onChange={(event) => logForm.setData('reflection', event.target.value)} />
                        </Field>
                        <Button className="self-end" disabled={logForm.processing}>
                            Log
                        </Button>
                    </form>
                </div>

                {path.logs.length > 0 && (
                    <div className="grid gap-2">
                        {path.logs.map((log) => (
                            <div key={log.id} className="bg-muted/20 rounded-md border p-3">
                                <div className="flex flex-wrap items-center gap-2 text-sm font-medium">
                                    <span>{log.topic}</span>
                                    <span className="text-muted-foreground">
                                        {log.learned_on} · {log.minutes}m
                                    </span>
                                </div>
                                <p className="text-muted-foreground mt-1 text-sm">{log.reflection}</p>
                            </div>
                        ))}
                    </div>
                )}
            </CardContent>
        </Card>
    );
}

function CheckpointActivity({
    checkpoint,
    pathId,
    onToggle,
    onDelete,
}: {
    checkpoint: LearningCheckpoint;
    pathId: number;
    onToggle: (checkpoint: LearningCheckpoint, isComplete: boolean) => void;
    onDelete: (checkpoint: LearningCheckpoint) => void;
}) {
    const isAnswered = checkpoint.activity_type === 'mcq' && checkpoint.user_answer !== null;
    const isWrongAnswer = isAnswered && !checkpoint.is_complete;

    function answerMcq(option: string) {
        router.put(
            route('learning-paths.checkpoints.update', [pathId, checkpoint.id]),
            {
                user_answer: option,
            },
            { preserveScroll: true },
        );
    }

    return (
        <div className="bg-muted/20 rounded-md p-3">
            <div className="flex items-start gap-3">
                {checkpoint.activity_type === 'task' ? (
                    <Checkbox
                        checked={checkpoint.is_complete}
                        onCheckedChange={(checked) => onToggle(checkpoint, checked === true)}
                        aria-label={`Mark ${checkpoint.title} complete`}
                    />
                ) : (
                    <div className="flex size-5 shrink-0 items-center justify-center rounded-full border text-[10px] font-bold">?</div>
                )}

                <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                        <p className={checkpoint.is_complete ? 'text-sm font-medium line-through opacity-60' : 'text-sm font-medium'}>
                            {checkpoint.title}
                        </p>
                        <Badge variant={checkpoint.activity_type === 'mcq' ? 'default' : 'secondary'}>{checkpoint.activity_type}</Badge>
                        <Badge variant="outline">{checkpoint.difficulty}</Badge>
                        {checkpoint.is_complete && <Badge className="bg-emerald-600 text-white hover:bg-emerald-600">done</Badge>}
                        {isWrongAnswer && <Badge variant="destructive">try again</Badge>}
                    </div>

                    {(checkpoint.prompt || checkpoint.notes) && (
                        <p className="text-muted-foreground mt-2 text-sm">{checkpoint.prompt ?? checkpoint.notes}</p>
                    )}

                    {checkpoint.activity_type === 'mcq' && checkpoint.options && (
                        <div className="mt-3 grid gap-2">
                            {checkpoint.options.map((option) => {
                                const isSelected = checkpoint.user_answer === option.key;
                                const isCorrect = checkpoint.is_complete && isSelected;

                                return (
                                    <Button
                                        key={option.key}
                                        type="button"
                                        variant={isSelected ? 'secondary' : 'outline'}
                                        className={
                                            isCorrect
                                                ? 'justify-start border-emerald-500 bg-emerald-500/10 text-left'
                                                : isWrongAnswer && isSelected
                                                  ? 'justify-start border-red-500 bg-red-500/10 text-left'
                                                  : 'justify-start text-left'
                                        }
                                        onClick={() => answerMcq(option.key)}
                                    >
                                        <span className="font-semibold">{option.key}.</span>
                                        <span className="whitespace-normal">{option.text}</span>
                                    </Button>
                                );
                            })}
                        </div>
                    )}

                    {checkpoint.explanation && (isAnswered || checkpoint.activity_type === 'task') && (
                        <p className="bg-background/70 text-muted-foreground mt-3 rounded-md border p-3 text-sm">{checkpoint.explanation}</p>
                    )}
                </div>

                <Button type="button" variant="ghost" size="icon" onClick={() => onDelete(checkpoint)} aria-label={`Delete ${checkpoint.title}`}>
                    <Trash2 />
                </Button>
            </div>
        </div>
    );
}

function StatCard({ icon: Icon, label, value }: { icon: LucideIcon; label: string; value: string }) {
    return (
        <Card>
            <CardContent className="flex items-center justify-between p-4">
                <div>
                    <p className="text-muted-foreground text-sm">{label}</p>
                    <p className="mt-1 text-2xl font-semibold">{value}</p>
                </div>
                <Icon className="text-muted-foreground size-5" />
            </CardContent>
        </Card>
    );
}

function Metric({ label, value }: { label: string; value: string }) {
    return (
        <div className="bg-muted/20 rounded-md border p-3">
            <p className="text-muted-foreground text-xs">{label}</p>
            <p className="mt-1 truncate text-sm font-medium">{value}</p>
        </div>
    );
}

function Field({ label, error, children }: { label: string; error?: string; children: ReactNode }) {
    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            {children}
            <InputError message={error} />
        </div>
    );
}

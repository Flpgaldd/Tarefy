import { spawn } from 'node:child_process';
import { cp, mkdir, readdir, rm } from 'node:fs/promises';
import path from 'node:path';
import { watch } from 'chokidar';

const sourceRoot = '/source/resources';
const targetRoot = '/app/resources';

let syncQueue = Promise.resolve();

function targetFor(sourcePath) {
    return path.join(targetRoot, path.relative(sourceRoot, sourcePath));
}

function enqueue(action) {
    syncQueue = syncQueue.then(action).catch((error) => {
        console.error('[sync] Falha ao sincronizar resources:', error);
    });
}

async function copyEntry(sourcePath) {
    const targetPath = targetFor(sourcePath);

    await mkdir(path.dirname(targetPath), { recursive: true });
    await cp(sourcePath, targetPath, {
        force: true,
        preserveTimestamps: true,
        recursive: true,
    });
}

async function removeEntry(sourcePath) {
    await rm(targetFor(sourcePath), { force: true, recursive: true });
}

async function initialSync() {
    await mkdir(targetRoot, { recursive: true });

    for (const entry of await readdir(targetRoot)) {
        await rm(path.join(targetRoot, entry), {
            force: true,
            recursive: true,
        });
    }

    await cp(sourceRoot, targetRoot, {
        force: true,
        preserveTimestamps: true,
        recursive: true,
    });

    console.log('[sync] Resources sincronizados.');
}

await initialSync();

const watcher = watch(sourceRoot, {
    awaitWriteFinish: {
        pollInterval: 100,
        stabilityThreshold: 200,
    },
    ignoreInitial: true,
    interval: 300,
    usePolling: true,
});

watcher
    .on('add', (sourcePath) => enqueue(() => copyEntry(sourcePath)))
    .on('addDir', (sourcePath) => {
        enqueue(() => mkdir(targetFor(sourcePath), { recursive: true }));
    })
    .on('change', (sourcePath) => enqueue(() => copyEntry(sourcePath)))
    .on('unlink', (sourcePath) => enqueue(() => removeEntry(sourcePath)))
    .on('unlinkDir', (sourcePath) => enqueue(() => removeEntry(sourcePath)))
    .on('error', (error) => {
        console.error('[sync] Erro no monitoramento:', error);
    });

const vite = spawn(
    'npm',
    ['run', 'dev', '--', '--host', '0.0.0.0'],
    { stdio: 'inherit' },
);

async function shutdown(signal) {
    await watcher.close();
    vite.kill(signal);
}

process.on('SIGINT', () => void shutdown('SIGINT'));
process.on('SIGTERM', () => void shutdown('SIGTERM'));

vite.on('exit', (code) => {
    process.exit(code ?? 0);
});

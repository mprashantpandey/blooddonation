<div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-5 shadow-xl sm:p-8">
    <h2 class="text-lg font-semibold text-zinc-900">Cron / Queue setup (cPanel shared hosting)</h2>
    <p class="mt-2 max-w-3xl text-sm text-zinc-600">
        On shared hosting you usually can’t run Supervisor. Use cron to process queued jobs (push notifications, etc).
        Run the worker every minute with <code class="rounded bg-black/5 px-1">--stop-when-empty</code> to prevent overlap.
    </p>

    <div class="mt-6 space-y-4">
        <div class="rounded-xl border border-zinc-200 bg-white/70 p-4">
            <div class="text-sm font-semibold text-zinc-900">Required .env</div>
            <pre class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 px-3 py-2 text-xs text-zinc-100">QUEUE_CONNECTION=database</pre>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white/70 p-4">
            <div class="text-sm font-semibold text-zinc-900">Queue worker cron (every minute)</div>
            <pre class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 px-3 py-2 text-xs text-zinc-100">cd /home/&lt;CPANEL_USER&gt;/public_html/&lt;PROJECT_ROOT&gt;/backend &amp;&amp; /usr/local/bin/php artisan queue:work --stop-when-empty --sleep=0 --tries=3 --timeout=60 &gt;&gt; /home/&lt;CPANEL_USER&gt;/bloodpulse-queue.log 2&gt;&amp;1</pre>
            <p class="mt-2 text-xs text-zinc-500">Replace <code class="rounded bg-black/5 px-1">&lt;CPANEL_USER&gt;</code> and <code class="rounded bg-black/5 px-1">&lt;PROJECT_ROOT&gt;</code>. Use your cPanel PHP binary path if different.</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white/70 p-4">
            <div class="text-sm font-semibold text-zinc-900">Scheduler cron (optional)</div>
            <pre class="mt-2 overflow-x-auto rounded-lg bg-zinc-950 px-3 py-2 text-xs text-zinc-100">cd /home/&lt;CPANEL_USER&gt;/public_html/&lt;PROJECT_ROOT&gt;/backend &amp;&amp; /usr/local/bin/php artisan schedule:run &gt;&gt; /home/&lt;CPANEL_USER&gt;/bloodpulse-schedule.log 2&gt;&amp;1</pre>
        </div>
    </div>
</div>


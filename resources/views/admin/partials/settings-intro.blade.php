<div class="mb-8">
    <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 sm:text-3xl">{{ $title }}</h1>
    @isset($description)
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-zinc-600 sm:text-base">{{ $description }}</p>
    @endisset
</div>

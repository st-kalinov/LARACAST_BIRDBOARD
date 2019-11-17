<div class="card flex flex-col" style="height: 200px">
    <h3 class="text-xl font-normal py-4 -ml-5 border-l-4 border-t-0 border-r-0 border-b-0 border-solid border-blue-light pl-4 mb-3">
        <a href="{{ $project->path() }}" class="no-underline text-default">{{ $project->title }}</a>
    </h3>

    <div class="text-default mb-4 flex-1">{{ \Illuminate\Support\Str::limit($project->description, 100) }}</div>

    @can('manage', $project)
        <footer>
            <form method="POST" action="{{ $project->path() }}" class="text-right">
                @method('DELETE')
                @csrf
                <button type="submit" class="text-xs">Delete</button>
            </form>
        </footer>
    @endcan
</div>

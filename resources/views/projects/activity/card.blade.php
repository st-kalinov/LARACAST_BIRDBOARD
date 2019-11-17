<div class="card mt-3">
    <ul>
        @foreach( $project->activity as $activity )
            <li>
                @include('projects.activity.' . $activity->description)
                <span class="text-grey">{{ $activity->created_at->diffForHumans(null, true) }}</span>
            </li>
        @endforeach
    </ul>
</div>

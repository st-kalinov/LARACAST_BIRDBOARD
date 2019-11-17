<div class="card flex flex-col mt-3">
    <h3 class="text-xl font-normal py-4 -ml-5 border-l-4 border-t-0 border-r-0 border-b-0 border-solid border-blue-light pl-4 mb-3">
        Invite a user
    </h3>

    <form method="POST" action="{{ $project->path() . '/invitations'}}" class="text-right">
        @csrf

        <div class="mb-3">
            <input type="email" name="email" id="" class="border border-grey rounded w-full py-2 px-3"
                   placeholder="Email address">
        </div>
        <button type="submit" class="text-xs button">Invite</button>
    </form>

    @include('errors', ['bag' => 'invitations'])
</div>

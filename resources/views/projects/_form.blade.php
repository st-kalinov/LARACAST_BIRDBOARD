<div>
    Title: <input type="text" name="title" id="" value="{{ $project->title }}"><br>
</div>
<div>
    Description: <textarea name="description" id="" cols="30" rows="5">{{ $project->description }}</textarea>
</div>

<input type="submit" value="{{ $buttonText }}">
<a href="{{ $project->path() }}">Cancel</a>
@if( $errors->any() )
    <div class="field mt-6 text-sm text-red">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </div>
@endif

@if(isset($results['results']))
<table>
    <thead>
    <tr>
        <th>observer id</th>
        <th>session id</th>
        <th>left image</th>
        <th>right image</th>
        <th>selected image</th>
        <th>time spent (in seconds)</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['results'] as $result)
            <tr>
                <td>{{ $result['observer'] }}</td>
                <td>{{ $result['session'] }}</td>
                <td>{{ $result['left'] }}</td>
                <td>{{ $result['right'] }}</td>
                <td>{{ $result['selected'] }}</td>
                <td>{{ $result['time_spent'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($results['observerInputs']))
<table>
    <thead>
    <tr>
        <th>observer</th>
        <th>meta (input title)</th>
        <th>answer</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['observerInputs'] as $input)
            <tr>
                <td>{{ $input['user_id'] }}</td>
                <td>{{ $input['observer_meta']['meta'] }}</td>
                <td>{{ $input['answer'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($results['imageSets']))
<table>
    <thead>
    <tr>
        <th>image set name</th>
        <th>image file name</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['imageSets'] as $imageSet)
            @foreach($imageSet['picture_set']['pictures'] as $image)
                <tr>
                    <td>{{ $imageSet['picture_set']['title'] }}</td>
                    <td>{{ $image['name'] }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@endif

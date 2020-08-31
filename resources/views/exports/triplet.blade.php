@if(isset($results['expMeta']))
<h2>Experiment parameters</h2>
<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">parameter</th>
        <th style="font-weight: bold;">value</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['expMeta'] as $expMeta)
            <tr>
                <td>{{ $expMeta[0] }}</td>
                <td>{{ $expMeta[1] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($results['imageSets']))
<h2>Image sets</h2>
<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">set name</th>
        <th style="font-weight: bold;">file name</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['imageSets'] as $imageSet)
            @foreach($imageSet['picture_set']['pictures'] as $image)
                <tr>
                    <td>{{ $imageSet['picture_set']['title'] }}</td>
                    <td>{{ $image['name'] }}</td>
                    @if($image['is_original'] == 1)
                        (reference/original)
                    @endif
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@endif

@if(isset($results['results']))
<h2>Stimuli Results</h2>
<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">observer id</th>
        <th style="font-weight: bold;">session id</th>
        <th style="font-weight: bold;">left image</th>
        <th style="font-weight: bold;">middle image</th>
        <th style="font-weight: bold;">right image</th>
        <th style="font-weight: bold;">left category</th>
        <th style="font-weight: bold;">middle category</th>
        <th style="font-weight: bold;">right category</th>
        <th style="font-weight: bold;">time spent (in seconds)</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['results'] as $result)
            <tr>
                <td>{{ $result['observer'] }}</td>
                <td>{{ $result['session'] }}</td>
                <td>{{ $result['picture_left'] }}</td>
                <td>{{ $result['picture_middle'] }}</td>
                <td>{{ $result['picture_right'] }}</td>
                <td>{{ $result['category_left'] }}</td>
                <td>{{ $result['category_middle'] }}</td>
                <td>{{ $result['category_right'] }}</td>
                <td>{{ $result['time_spent'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($results['inputsMeta']))
<h2>Inputs meta data</h2>
<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">id</th>
        <th style="font-weight: bold;">input title</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['inputsMeta'] as $meta)
            <tr>
                <td>{{ $meta['id'] }}</td>
                <td>{{ $meta['observer_meta']['meta'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($results['inputs']))
<h2>Inputs results</h2>
<table>
    <thead>
    <tr>
        <th style="font-weight: bold;">observer</th>
        <th style="font-weight: bold;">input title</th>
        <th style="font-weight: bold;">answer</th>
    </tr>
    </thead>
    <tbody>
        @foreach($results['inputs'] as $input)
            <tr>
                <td>{{ $input['experiment_result']['user']['id'] }}</td>
                <td>{{ $input['observer_meta']['meta'] }}</td>
                <td>{{ $input['answer'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

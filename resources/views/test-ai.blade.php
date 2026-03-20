<h2>AI Recommendations Test Page</h2>

@if(count($recommended) == 0)
    <h3 style="color:red;">❌ No AI workers found</h3>
@else
    <h3 style="color:green;">✔ AI Workers Loaded Successfully</h3>

    @foreach($recommended as $w)
        <div style="margin:10px; padding:10px; border:1px solid #ccc;">
            <p><strong>Name:</strong> {{ $w['name'] }}</p>
            <p><strong>Skills:</strong> {{ $w['skills'] }}</p>
            <p><strong>Experience:</strong> {{ $w['experience'] }}</p>
            <p><strong>Photo:</strong></p>
            <img src="{{ $w['photo'] }}" width="60" height="60">
        </div>
    @endforeach
@endif

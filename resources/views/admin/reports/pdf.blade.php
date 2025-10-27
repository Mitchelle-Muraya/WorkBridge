<!DOCTYPE html>
<html>
<head>
    <title>WorkBridge Job Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { text-align: center; color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h1>WorkBridge Job Report</h1>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Job Title</th>
                <th>Status</th>
                <th>Client ID</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $job->title }}</td>
                    <td>{{ $job->status }}</td>
                    <td>{{ $job->client_id }}</td>
                    <td>{{ $job->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

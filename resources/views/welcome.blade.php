@vite('resources/js/app.js')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Your Name</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
        <div class="card shadow-sm" style="width: 380px;">
            <div class="card-body">
                <h5 class="card-title mb-4 text-center">Enter Your Name</h5>

                <form action="{{url('/chat')}}" method="GET">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="sender_name" class="form-control" required>
                    </div>

                    <button class="btn btn-primary w-100">Enter Chat</button>
                </form>
            </div>
        </div>
    </div>





</body>
</html>

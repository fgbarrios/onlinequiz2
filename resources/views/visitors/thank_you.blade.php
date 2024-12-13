<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f0f0f0;
        }
        .thank-you-container {
            text-align: center;
            padding: 50px;
        }
        .thank-you-image {
            width: 35%;
            height: auto;
        }
        @media (max-width:768px)
        {
            .thank-you-image {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 thank-you-container">
                <h1>Thank You!</h1>
                <h3>For the participation</h3>
                <p>Your response has been submitted.</p>
                <img src="{{asset('admin/assets/img/thankyou.svg')}}" alt="Thank You Image" class="thank-you-image">
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional, for Bootstrap features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>

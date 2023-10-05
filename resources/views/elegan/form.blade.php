<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="{{ asset('elegan/favicon.ico') }}" type="image/x-icon">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Elegan Docs</title>
    <link href="{{ asset('elegan/assets/access-docs.css') }}" rel="stylesheet">
</head>

<body>
    <section id="form-section">
        <form action="/api/docs" method="POST">
            <h1>Acessar o Elegan</h1>
            <input name="_method" type="hidden" value="GET">
            <svg version="1.1" id="Camada_1" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 200 200"
                style="enable-background:new 0 0 200 200;" xml:space="preserve" class="image">
                <style type="text/css">
                    .st0 {
                        fill: #BF0A0A;
                    }
                </style>
                <g transform="translate(0.000000,200.000000) scale(0.100000,-0.100000)">
                    <path
                        d="M758.2,1575.4c-220-35-287-100-231-225c30-65,41-136,41-255v-110l33,6c235,40,543,44,798,9l46-6l6,178c4,111,12,206,22,249
		c26,110,27,108-30,126c-27,9-102,22-166,28C1144.2,1588.4,839.2,1588.4,758.2,1575.4z" />
                    <path class="st0"
                        d="M755.2,974.4c-160-18-164-23-29-37c132-14,292-51,545-125c175-51,211-56,203-28c-3,9-8,50-12,89l-7,73l-36,10
		C1315.2,983.4,930.2,994.4,755.2,974.4z" />
                    <path
                        d="M524.2,880.4c-124-45-181-158-123-245c20-32,143-106,155-94c2,2-10,16-27,31c-38,32-71,93-71,131c0,63,74,112,166,113
		c83,0,265-37,417-86c266-86,501-117,591-80c88,37,88,131,1,152c-30,7-30,7-12-14c42-46-8-82-115-82c-84,0-192,24-358,80
		c-183,61-234,75-344,95C695.2,900.4,576.2,899.4,524.2,880.4z" />
                </g>
            </svg>
            <label for="key" class="text-dark">Chave do Elegan</label>
            <input type="password" name="key" id="key" required>

            @isset($_GET['message'])
                <div class="alert">
                    <span>{{ $_GET['message'] }}</span>
                </div>
            @endisset

            Tentativas: {{ RateLimiter::remaining(Request::ip(), config('elegan.rate_limit')) + 1 }}
            <button type="submit">Acessar</button>
        </form>
    </section>
</body>

</html>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{config('app.name')}} - @yield("title")</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                margin: 0;
                padding: 0;
            }
            header, footer {
                text-align: center;
                padding: 10px 0;
                /* background-color: #f1f1f1; */
            }
            .header-color {
                color: #006666; 
            }
            /* main {
                padding: 20px;
            } */
            .table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .table-custom-width {
                width: 300px !important;
            }
            .table-bordered {
                border: 1px solid #999999;
                border-collapse: collapse;
            }

            .table-bordered th,
            .table-bordered td {
                border: 0.5px solid #999999; 
                padding: 8px;
                text-align: center;
            }
            th, td {
                padding: 8px;
                text-align: center;
            }
            th {
                background-color: #006666;
                color: white;
            }
            .text-end {
                text-align: right;
            }
            .fw-bold {
                font-weight: bold;
            }
            .w-300px {
                width: 300px;
            }

            .bg-light {
                background-color:#E6E9EB;
            }
            
            .border-0 {
                border: none !important;
            }

            .border {
                border: 1px solid #999999 !important;
            }
            .mb-0 {
                margin-bottom: 0px !important;
            }
            .p-1 {
                padding: 10px !important;
            }
            .max-width-500px {
                max-width: 500px !important;
            }
            
            .p-0 {
                padding: 0px !important;
            }
        </style>
    </head>
    <body>
        @yield('content')
    </body>
</html>
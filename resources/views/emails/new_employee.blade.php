

@include('partials.__header')

<h1>New Employee Added</h1>
    <p>We are excited to announce a new employee:</p>
    <p><strong>{{ $displayName }}</strong></p>
    <p>Click the link below to view the employee details:</p>
    <!-- <p><a href="{{ url('/employee/' . $employeeId) }}">View Employee</a></p> -->



@include('partials.__footer')
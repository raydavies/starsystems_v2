<html>
<body>
    <p><strong>Lead Source: Star Learning Systems - Customer Testimonial</strong></p>
    <p>Customer Name: {{ $testimonial->name or 'Anonymous' }}</p>
    <p>Location: @if ($testimonial->city){{ $testimonial->city }}, @endif{{ $testimonial->state_province }}</p>
    <p>Comments: {{ $testimonial->comment }}</p>
</body>
</html>

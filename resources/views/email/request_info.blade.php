<html>
<body style="background-image: url({{ asset('img/bg/sky3.jpg') }})">
	<img src="{{ asset('img/starlogo-small.png') }}" />
	<div style="background-color: #fff; border-radius: 7px; margin: 10px; padding: 5px 15px 15px;">
		<p>Jim,<br> A customer has submitted a request for more information about the Interactive Curriculum! Here are the details:</p>
		<p>
			<label><strong>Name:</strong></label> {{ $customer->name }}<br>
			<label><strong>Email:</strong></label> {{ $customer->email }}<br>
			<label><strong>Home Phone:</strong></label> {{ $customer->phone_home }}<br>
			
			@if ($customer->child_name)
				<label><strong>Child's Name:</strong></label> {{ $customer->child_name }}<br>
			@endif

			@if ($customer->fullGrade)
				<label><strong>Child's Grade:</strong></label> {{ $customer->fullGrade->name }}<br>
			@endif
		</p>
		<p><a href="{{ route('admin.customer', ['customer' => $customer->id]) }}" target="_blank">Click here to manage this customer in the Admin CRM</a></p>
	</div>
</body>
</html>

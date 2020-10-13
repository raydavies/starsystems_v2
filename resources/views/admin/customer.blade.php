@extends('layout.dashboard')

@section('title', $customer->name)

@section('content')
    <div class="row">
        <a href="{{ route('admin.customers') }}"><i class="fas fa-angle-left"></i> Back to Customer Database</a>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Customer {{ $customer->id }} - {{ $customer->name }}</strong></div>
        <div class="panel-body">
            <address>
                <label>Email:</label> <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a><br>
                <label>Home Phone:</label> <a href="tel:{{ $customer->phone_home }}">{{ $customer->phone_home }}</a>
                @if ($customer->phone_work)
                    <label>Work Phone:</label> <a href="tel:{{ $customer->phone_work }}">{{ $customer->phone_work }}</a>
                @endif
            </address>
            
            @if ($customer->street_address)
                <div>
                    <label>Address:</label>
                    <address>
                        {{ $customer->street_address }}<br>
                        {{ $customer->city }}, {{ $customer->state_province }} {{ $customer->zip_code }}
                    </address>
                </div>
            @endif
            
            @if ($customer->child_name)
                <div>
                    <label>Child's Name:</label> <span>{{ $customer->child_name }}</span>
                </div>
            @endif
            
            @if ($customer->fullGrade)
                <div>
                    <label>Child's Grade:</label> <span>{{ $customer->fullGrade->name }}</span>
                </div>
            @endif
        </div>

        @if ($customer->contactHistory)
            <ul class="list-group">
                @foreach ($customer->contactHistory as $contact)
                    <li class="list-group-item">
                        <label>@datetime($contact->occurred_at)</label>
                        <p><strong>{{ $contact->action }}</strong> - {{ $contact->details }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="row bg-warning" style="padding: 10px; border-radius: 5px;">
        <div class="text-center" style="margin-bottom: 10px;">
            <header class="header">Add new customer contact note</header>
        </div>
        <form class="form-horizontal" method="post" action={{ route('admin.add_customer_contact') }} accept-charset="UTF-8" id="add_customer_contact_form" novalidate="novalidate">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}" />
            <div class="form-group">
                <label for="contact_action" class="col-sm-2 control-label">Action</label>
                <div class="col-sm-10 col-md-4">
                    <select required name="action" id="contact_action" class="form-control" aria-describedby="action_status">
                        <option value="CALLED">Called Customer</option>
                        <option value="EMAILED">Emailed Customer</option>
                        <option value="FOLLOW UP">Followed Up With Customer</option>
                        <option value="SCHEDULED">Scheduled Visit With Customer</option>
                        <option value="VISITED">Visited Customer at Home</option>
                        <option value="SOLD">Sold Product To Customer</option>
                    </select>
                    <span id="action_status" class="sr-only hidden"></span>
                </div>
                <div class="control-message">
                    <span class="errormsg">{{ $errors->first('action') }}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="contact_details" class="col-sm-2 control-label">Details</label>
                <div class="col-sm-10 col-md-6">
                    <textarea required name="details" id="contact_details" class="form-control" rows="4" aria-describedby="details_status"></textarea>
                    <span id="details_status" class="sr-only hidden"></span>
                </div>
                <div class="control-message">
                    <span class="errormsg">{{ $errors->first('details') }}</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
    </div>
@stop
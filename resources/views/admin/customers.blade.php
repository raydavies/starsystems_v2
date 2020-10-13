@extends('layout.dashboard')

@section('title', 'Customer Database')

@section('content')
    <header class="header header-xl">Customer Database</header>
    <div class="row">
        <p class="alert alert-info" role="alert">Use this widget to manage customer records generated by the Request More Information form.</p>
    </div>

    <div id="customer_manager">
        <section id="customer_list" class="text-left">
            @if (count($customers))
                <div class="row clearfix">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th></th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Home Phone</th>
                                <th scope="col">Work Phone</th>
                                <th scope="col">Address</th>
                                <th scope="col">City</th>
                                <th scope="col">State</th>
                                <th scope="col">Zip</th>
                                <th scope="col">Child's Name</th>
                                <th scope="col">Child's Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $index => $customer)
                                <tr>
                                    <th scope="row">@if (!$customer->contactHistory->count())<span class="label label-primary">New</span>@endif</th>
                                    <td><a href="{{ route('admin.customer', ['customer' => $customer->id]) }}">{{ $customer->name }}</a></td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone_home }}</td>
                                    <td>{{ $customer->phone_work }}</td>
                                    <td>{{ $customer->street_address }}</td>
                                    <td>{{ $customer->city }}</td>
                                    <td>{{ $customer->state_province }}</td>
                                    <td>{{ $customer->zip_code }}</td>
                                    <td>{{ $customer->child_name }}</td>
                                    <td>{{ $customer->fullGrade->name ?? null }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="row text-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="@if ($currentPage <= 1) disabled @endif">
                                <a href="@if ($currentPage <= 1) # @else {{ route('admin.customers', ['page' => $currentPage - 1]) }} @endif" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            @for ($i = 1; $i <= $totalPages; $i++)
                                <li class="@if ($i === $currentPage) active @endif">
                                    <a href="@if ($i === $currentPage) # @else {{ route('admin.customers', ['page' => $i]) }} @endif">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="@if ($currentPage >= $totalPages) disabled @endif">
                                <a href="@if ($currentPage >= $totalPages) # @else {{ route('admin.customers', ['page' => $currentPage + 1]) }} @endif" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @else
                <div class="row">
                    <p class="alert alert-warning text-center" role="alert">
                        No customers found.
                        @if ($currentPage !== 1) <a href="{{ route('admin.customers', ['page' => 1]) }}">Click here to navigate back to the first page of results</a> @endif
                    </p>
                </div>
            @endif
        </section>
    </div>
@stop

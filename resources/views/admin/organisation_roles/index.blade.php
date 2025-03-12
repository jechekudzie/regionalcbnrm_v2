@extends('layouts.backend')

@push('head')

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css"/>

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')

    <div class="pb-5">
        <div class="row g-4">
            <div class="col-12 col-xxl-12">
                <div class="mb-8">
                    <h2 class="mb-2">{{$organisation->name}}</h2>
                    <h5 class="text-700 fw-semi-bold">Roles and Permissions</h5>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary px-5" href="{{route('admin.organisations.manage')}}">
                        <i class="fa-solid fa-caret-left me-2"></i>
                        Back to organisations
                    </a>

                    <a class="btn btn-primary px-5" href="{{route('admin.organisation-roles.index',$organisation->slug)}}">
                        <i class="fa-solid fa-plus me-2"></i>
                        Add new role
                    </a>
                </div>

                <br/>
                <div id="messageContainer"></div>
                <div id="errorContainer"></div>
                <!-- Start custom content -->
                <div class="row g-4">

                    <div class="col-9 col-xl-9">
                        <div class="mb-9">
                            <div class="card shadow-none border border-300 my-4"
                                 data-component-card="data-component-card">
                                <div class="card-header p-4 border-bottom border-300 bg-soft">
                                    <div class="row g-3 justify-content-between align-items-center">
                                        <div class="col-12 col-md">
                                            <h4 class="text-900 mb-0 card-title" data-anchor="data-anchor"> {{$organisation->name}} - Roles and Permissions</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-4 code-to-copy">
                                        <div>
                                            <div class="table-responsive">
                                                <table style="width: 100%;" id="buttons-datatables"
                                                       class="display table table-bordered dataTable no-footer"
                                                       aria-describedby="buttons-datatables_info">
                                                    <thead>
                                                    <tr>
                                                        <th class="sorting sorting_asc" tabindex="0"
                                                        >#
                                                        </th>
                                                        <th class="sorting" tabindex="0">Role
                                                        </th>

                                                        <th class="sorting" tabindex="0" >Guard name
                                                        </th>

                                                        <th class="sorting" tabindex="0">Permissions</th>

                                                        <th class="sorting" tabindex="0">Action</th>

                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($roles as $role)
                                                        <tr class="even">
                                                            <td class="sorting_1">{{$loop->iteration}}</td>
                                                            <td>{{$role->name}}</td>
                                                            <td>{{$role->guard_name}}</td>


                                                            <td>
                                                                <a href="{{route('admin.permissions.assign',[$organisation->slug,$role->id])}}"
                                                                   class="btn btn-sm btn-primary" title="View permissions">
                                                                    <i class="fa fa-terminal"></i> Assign Permissions
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <!-- Edit Button -->
                                                                <a href="javascript:void(0);" class="edit-button btn btn-sm btn-primary"
                                                                   data-name="{{ $role->name }}" data-id="{{ $role->id }}" title="Edit">
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>

                                                                <!-- Delete Button -->
                                                                <form
                                                                    action="{{ route('admin.organisation-roles.destroy', $role->id) }}"
                                                                    method="POST" onsubmit="return confirm('Are you sure?');"
                                                                    style="display: inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 col-xl-3">
                        <div class="mb-9">
                            <div class="card shadow-none border border-300 my-4"
                                 data-component-card="data-component-card">
                                <div class="card-header p-4 border-bottom border-300 bg-soft">
                                    <div class="row g-3 justify-content-between align-items-center">
                                        <div class="col-12 col-md">
                                            <h4 id="card-title" class="text-900 mb-0" data-anchor="data-anchor">Add Role</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-4 code-to-copy">
                                        <form id="edit-form"
                                              action="{{route('admin.organisation-roles.store',$organisation->slug)}}"
                                              method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="_method" value="POST">
                                            @csrf
                                            <div class="mb-3">

                                                <label class="form-label" for="exampleFormControlInput">Role</label>
                                                <input class="form-control" name="name" id="name" type="text"
                                                       placeholder="Enter role eg. finance officer"/>
                                            </div>

                                            <hr/>
                                            <div class="col-12">
                                                <div class="row ">
                                                    <div >
                                                        <button id="submit-button" class="btn btn-primary btn-sm w-100">Add New Role</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- end custom content -->

            </div>


        </div>
    </div>

    <script>

    </script>

@endsection

@push('scripts')
    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script>
        <!-- datatable js -->
        document.addEventListener("DOMContentLoaded", function () {
            $('#buttons-datatables').DataTable({
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print', 'pdf']
            });
        });

        // Assuming you have jQuery available
        $(document).ready(function () {
            // Define the submit button
            var submitButton = $('#submit-button'); // Replace with your actual button ID or class
            submitButton.text('Add New');
            //on load by default name field to be empty
            $('#name').val('');
            var organisation_id = $('#organisation_id').val();

            // Click event for the edit button
            $('.edit-button').on('click', function () {
                var name = $(this).data('name');
                var id = $(this).data('id');


                // Set form action for update, method to PATCH, and button text to Update
                $('#edit-form').attr('action', '/admin/organisation-roles/' + id + '/update');
                $('input[name="_method"]').val('PATCH');
                submitButton.text('Update');
                // Populate the form for editing
                $('#name').val(name);
                $('#card-title').text('Edit - ' + name + 'Organisation role');
                $('#page-title').text('Edit - ' + name + ' Organisation role');
            });

            // Click event for adding a new item
            $('#new-button').on('click', function () {
                // Clear the form, set action for creation, method to POST, and button text to Add New
                $('input[name="_method"]').val('POST');
                submitButton.text('Add New');
                $('#name').val('');
                $('#card-title').text('Add Organisation role');
                $('#page-title').text('Add New Organisation role');
            });
        });
    </script>

@endpush

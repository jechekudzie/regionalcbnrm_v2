@extends('layouts.backend')

@section('content')

    <div class="pb-5">
        <div class="row g-4">
            <div class="col-12 col-xxl-12">
                <div class="mb-8">
                    <h2 class="mb-2">Organisational Structure - Setup</h2>
                    <h5 class="text-700 fw-semi-bold">The hierarchy for ZANU PFS.</h5>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary px-5" href="{{route('admin.organisation-types.index')}}">
                        <i class="fa-solid fa-refresh me-2"></i>
                        Refresh
                    </a>

                </div>
                <br/>
                <div id="messageContainer"></div>
                <div id="errorContainer"></div>
                <!-- Start custom content -->
                <div class="row g-4">

                    <div class="col-md-4 col-xl-4">
                        <div class="mb-9">
                            <div class="card shadow-none border border-300 my-4"
                                 data-component-card="data-component-card">
                                <div class="card-header p-4 border-bottom border-300 bg-soft">
                                    <div class="row g-3 justify-content-between align-items-center">
                                        <div class="col-12 col-md">
                                            <h4 class="text-900 mb-0" data-anchor="data-anchor">Structure</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-4 code-to-copy">
                                        <!--start tree-->
                                        <div id="tree"></div>
                                        <!--end tree-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 col-xl-8 ">
                        <div class="mb-9">
                            <div class="card shadow-none border border-300 my-4"
                                 data-component-card="data-component-card">
                                <div class="card-header p-4 border-bottom border-300 bg-soft">
                                    <div class="row g-3 justify-content-between align-items-center">
                                        <div class="col-12 col-md">
                                            <h4 class="text-900 mb-0" data-anchor="data-anchor">Add Hierarchy Structure</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="p-4 code-to-copy">
                                        <form id="organisationTypeform" action="/admin/organisation-types/store"
                                              method="post"
                                              enctype="multipart/form-data">
                                            <input type="hidden" name="_method" value="POST">
                                            @csrf
                                            <div class="mb-3">

                                                <label class="form-label" for="exampleFormControlInput">Structure</label>

                                                <input class="form-control" name="name" id="name" type="text"
                                                       placeholder="Enter Organisation Structure"/>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label" for="exampleTextarea">Describe Organisation Type</label>
                                                <textarea name="description" class="form-control" id="description" rows="4"></textarea>
                                            </div>

                                            <hr/>

                                            <div class="col-12">
                                                <div class="row ">
                                                    <div >
                                                        <button id="submit-button" class="btn btn-primary btn-sm">Add To Structure</button>
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

        $(document).ready(function () {
            //set up laravel ajax csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            // Initialize the tree
            var tree = $('#tree').tree({
                primaryKey: 'id',
                dataSource: '/api/admin/organisation-types',
                uiLibrary: 'bootstrap4',
                cascadeCheck: false,
            });

            var hiddenNodeIdField = $('#hiddenNodeId');
            var checkedNodeNameElement = $('#checkedNodeName');
            var submitButton = $('#submit-button');
            var cardTitle = $('#card-title');
            var pageTitle = $('#page-title');

            let primaryNodeId = null;
            let nodeName = null;
            let organisationTypeName = null;
            let organisationTypeSlug = null;
            let actionUrl = null;
            actionUrl = '/admin/organisation-types/store';
            // Handle node selection
            tree.on('select', function (e, $node, id) {
                saveSelectedNodeId(id);
                var nodeData = tree.getDataById(id);

                if (nodeData) {
                    primaryNodeId = nodeData.id;
                    nodeName = nodeData.text;
                    organisationTypeName = nodeName;
                    organisationTypeSlug = nodeData.slug;

                    cardTitle.text('Add - ' + organisationTypeName + ' (children organisation types)');
                    pageTitle.text('Add - ' + organisationTypeName + ' (children organisation types)');
                    submitButton.text('Add ' + organisationTypeName + '(children organisation types)');
                    /*$('#organisationTypeform').attr('action', '/admin/organisation-types/' + organisationTypeSlug);*/
                    actionUrl = '/admin/organisation-types/' + organisationTypeSlug;

                    hiddenNodeIdField.val(primaryNodeId);
                    checkedNodeNameElement.text(nodeName);

                    // Clear form fields
                    $('#name').val('');
                    $('#description').val('');
                }
            });
            tree.on('unselect', function (e, node, id) {
                actionUrl = '/admin/organisation-types/store';
                clearSavedNodeId();
            });

            $('#organisationTypeform').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                var formData = $(this).serialize(); // Serialize the form data

                $.ajax({
                    type: 'POST',
                    url: actionUrl, // The URL to the server-side script that will process the form data
                    data: formData,
                    success: function (response) {
                        $('#organisationTypeform').trigger('reset');

                        // Set the flag to true and reload the tree
                        treeReloaded = true;
                        tree.reload();

                        if (response.success) {
                            // Display success message
                            $('#messageContainer').html('<div class="alert alert-outline-success d-flex align-items-center" role="alert">' +
                                '<span class="fas fa-check-circle text-success fs-3 me-3"></span>' +
                                '<p class="mb-0 flex-1">' + response.message + '</p>' +
                                '<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>');
                        }
                        // Set a timeout to hide the message container after 5000 milliseconds
                        setTimeout(function() {
                            $('#messageContainer').fadeOut('slow');
                        }, 5000); // 5000 milliseconds = 5 seconds

                        console.log('Form successfully submitted. Server responded with: ' + response);
                    },
                    error: function (xhr) { // Added 'xhr' parameter to access response
                        if (xhr.status === 422) { // Validation Error
                            var errors = xhr.responseJSON.errors;
                            var errorsHtml = '';
                            $.each(errors, function (key, value) {
                                errorsHtml += '<div class="alert alert-outline-danger d-flex align-items-center" role="alert">' +
                                    '<span class="fas fa-times-circle text-danger fs-3 me-3"></span>' +
                                    '<p class="mb-0 flex-1">' + value[0] + '</p>' + // Use 'value[0]' as the message
                                    '<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>';
                            });
                            $('#errorContainer').html(errorsHtml);
                            //Set a timeout to hide the message container after 5000 milliseconds
                            setTimeout(function() {
                                $('#errorContainer').fadeOut('slow');
                            }, 5000); // 5000 milliseconds = 5 seconds
                        } else {
                            // Handle other kinds of errors
                            console.error('An error occurred while submitting the form.');
                        }
                    }
                });
            });

            var treeReloaded = true; // Flag to check if tree has been reloaded

            // Function to save selected node ID to local storage
            function saveSelectedNodeId(nodeId) {
                localStorage.setItem('selectedNodeId', nodeId);
            }

            // Function to get selected node ID from local storage
            function getSelectedNodeId() {
                return localStorage.getItem('selectedNodeId');
            }

            // Function to clear the saved node ID from local storage
            function clearSavedNodeId() {
                localStorage.removeItem('selectedNodeId');
            }

            // Function to expand from root to a given node
            function expandFromRootToNode(nodeId) {
                var parents = tree.parents(nodeId);
                if (parents && parents.length) {
                    parents.reverse().forEach(function (parentId) {
                        tree.expand(parentId);
                    });
                }
                tree.expand(nodeId);
            }

            // Function to select and expand from root to a node by ID
            function selectAndExpandFromRootToNode(nodeId) {
                console.log("Selecting and expanding node: ", nodeId);
                var nodeToSelect = tree.getNodeById(nodeId);
                if (nodeToSelect) {
                    tree.select(nodeToSelect);  // Selects the node
                    expandFromRootToNode(nodeId);  // Expands from root to the node
                } else {
                    console.log("Node not found: ", nodeId);
                }
            }

            // Select and expand from root to the node if it's saved in local storage
            var savedNodeId = getSelectedNodeId();
            if (savedNodeId) {
                selectAndExpandFromRootToNode(savedNodeId);
            }

            // Event listener for node selection
            tree.on('select', function (e, node, id) {
                saveSelectedNodeId(id);
            });

            // Event listener for node unselection (if applicable)
            // Replace 'unselect' with the correct event name if different
            tree.on('unselect', function (e, node, id) {
                clearSavedNodeId();
            });

            // Handle the dataBound event
            tree.on('dataBound', function () {
                if (treeReloaded) {
                    var savedNodeId = getSelectedNodeId();
                    if (savedNodeId) {
                        selectAndExpandFromRootToNode(savedNodeId);
                    }
                    // Reset the flag
                    treeReloaded = false;
                }
            });
        });


    </script>

@endsection

@extends('layout.index')
@section('title', 'Organiser ')

<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header w-100">
                            <div class="content-header-left">
                                <div class="row breadcrumbs-top">
                                    <div class="col-sm-12">
                                        <h2 class="content-header-title float-left mb-0">Organiser List</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                        <li class="breadcrumb-item">Organiser</li>
                                        <li class="breadcrumb-item active" aria-current="page">Organiser List</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bordered table end -->
        </div>
        @if ($message = Session::get('success'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <i class="fa fa-check-circle" style="font-size:16px;" aria-hidden="true"></i>
                            {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @elseif ($message = Session::get('error'))
            <div class="demo-spacing-0 mb-1">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="alert-body">
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
           </div>
        @endif

        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card ">
                        <form class="dt_adv_search" action="{{ url('users') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_user">
                            <div class="card-header w-100 m-0"> 
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        <div class="row">
                                          
                                        </div>
                                    </div>
                                </div>
                              
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr. No</th>
                                        <th class="text-left">Organiser Name</th>
                                        <th class="text-left">User Name</th>                                    
                                        <th class="text-left">Email ID</th>
                                        <th class="text-left">Contact Number</th>
                                        <th class="text-left">Gst Number</th>
                                       
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                    
                                    <?php 
                                    if (!empty($OrganiserDetails)){
                                        $i = $Offset;
                                        // $i = 0;
                                        ?>
                                        <?php foreach ($OrganiserDetails as $val){
                                                $i++;?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                               
                                                <td class="text-left">{{ $val->name }}</td>
                                                <td class="text-left">{{ $val->user_name }}</td>
                                                <td class="text-left">{{ $val->email }}</td>
                                                <td class="text-left">{{ $val->mobile }}</td>
                                                <td class="text-left">{{ $val->gst_number }}</td>
                                              
                                                <td>
                                                    <!-- <a href="http://localhost:3000/in/Nashik/<?php //echo $val->id; ?>/<?php //echo $val->user_email; ?>/<?php //echo $val->user_password; ?>" target="_blank">
                                                        <i class="fa fa-eye btn btn-primary btn-sm" title="Login As Organiser"></i>
                                                    </a> -->
                                                </td>
                                            </tr>
                                      <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
                                        </tr>
                                  <?php }?>
                                </tbody>
                            </table>
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    {{ $Paginator->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>
    </section>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@extends('layout.index')
@section('title', 'Kpi List')

<!-- Dashboard Ecommerce start -->
@section('content')
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-7 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Kpi List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Master</a>
                        </li>
                        <li class="breadcrumb-item active">Kpi List
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>       
  </section>
    <section>
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
                        <form class="dt_adv_search"  method="POST">
                            @csrf
                            <input type="hidden" name='form_type' value='search_master_brand'>
                            <div class="card-header w-100 m-0">
                                <div class="row w-100">
                                    <div class="col-sm-12">
                                        {{-- <a href="{{ url('/master_programs/add') }}" class="btn btn-outline-primary float-right">
                                            <i data-feather="plus"></i><span>Add Program</span></a> --}}
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive text-center">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Kpi Name</th>
                                        {{-- <th class="text-left">Active</th> --}}
                                        <th >Actions</th> 
                                    </tr>
                                </thead>
                                <tbody >
                                
                                    <?php 
                                    if (!empty($kpi_data)){
                                       $i =0;?>
                                        <?php foreach ($kpi_data as $val){
                                          
                                                $i++;?>
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $val->name }}</td>
                                                <td>
                                                    <a href="{{ url('add_question/'.$val->id) }}"><i
                                                            class="fa fa-edit btn btn-primary btn-sm " title="Add Question"></i></a>
                                                </td> 
                                                                                      
                                            </tr>
                                      <?php }
                                    }else{?>
                                        <tr>
                                            <td colspan="8" style="text-align:center; color:red;">No Record Found</td>
                                        </tr>
                                  <?php }?>
                                </tbody>
                            </table>
                           
                        </div>
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>


    </section>

@endsection

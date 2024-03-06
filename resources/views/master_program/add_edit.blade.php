@extends('layout.index')
@if (isset($id))
    @section('title', 'Edit Program Details')
@else
    @section('title', 'Add Program Details')
@endif
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-7 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0">
                                @if (isset($id))
                                Edit Program
                                @else
                                Add Program
                                @endif
                                </h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-5 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Program</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @if (isset($id))
                                    Edit Program
                                    @else
                                    Add Program
                                    @endif
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        {{-- <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> --}}
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
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="form"
								 id="categoryform" action="" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="form_type" value="add_edit_master_program">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="program_name">Program Name <span style="color:red;">*</span></label>
                                                <input type="text" id="program_name" class="form-control"
                                                    placeholder="Enter Program Name" name="program_name"
                                                    value="{{ old('program_name',$program_name) }}"
                                                    autocomplete="off" />
                                                @error('program_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <h5><small class="text-danger" id="program_name_err"></small></h5>
                                            </div>
                                        </div>
                                       
                                    </div>   
                                    

                                    <div class="col-12 text-center mt-1">
                                        <input type="submit" class="btn btn-primary mr-1" onClick="return validation()" value="Submit">
                                        <a href="{{ url('/master_programs') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php $live_url = config('custom.base_url'); ?>
    </section>
    
   
@endsection




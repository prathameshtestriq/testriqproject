@extends('layout.index')
{{-- @if (!empty($id)) --}}
    @section('title', ' Master CMS Management')
{{-- @else --}}
    @section('title', ' Master CMS Management')
{{-- @endif --}}

@section('title', 'Remittance  Create')
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <style>
            .ck-editor__editable {
                min-height: 250px; /* Set the minimum height as needed */
            }
        </style>
        </style>

        <div class="content-body">
            <!-- Bordered table start -->
            <div class="row" id="table-bordered">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header w-100">
                            <div class="content-header-left">
                                <div class="row breadcrumbs-top">
                                    <div class="col-sm-12">
                                        <h2 class="content-header-title float-left mb-0">
                                            {{-- @if (!empty($aReturn['id'])) --}}
                                                Edit Master CMS  Details
                                            {{-- @else --}}
                                                Add MASTER CMS  Details
                                            {{-- @endif --}}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end breadcrumb-wrapper">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mr-1">
                                        <li class="breadcrumb-item">Home</li>
                                        <li class="breadcrumb-item">Remittance</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            {{-- @if (!empty($aReturn['id'])) --}}
                                                Edit Master CMS   
                                            {{-- @else --}}
                                                Add Master CMS 
                                            {{-- @endif  --}}
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bordered table end -->
        </div>

        @if ($message = Session::get('error'))
            <div class="demo-spacing-0 mb-1">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                                <form class="form" action="" method="post">
                                    <input type="hidden" name="form_type" value="add_edit_master_cms"
                                        enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="title"> Title <span style="color:red;">*</span></label>
                                                <input type="text" id="title" class="form-control"
                                                    placeholder="Title" name="title"  value="{{ old('title', $title) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="title_err"></small></h5>
                                                @error('title')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label for="cms_description"> Description <span style="color:red;">*</span></label>
                                                <textarea id="cms_description" class="form-control"
                                                name="cms_description" autocomplete="off" >{{ old('cms_description', $description) }}</textarea>
                                                <h5><small class="text-danger" id="cms_description_err"></small></h5>
                                                @error('cms_description')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                     

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/master_cms') }}" type="reset"
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
    </section>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src={{ asset('/app-assets/js/scripts/Ckeditor/ckeditor.js') }}></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
        .create(document.querySelector('#cms_description'), {
            ckfinder: {
                uploadUrl: '{{ route('ckeditor_master_cms.upload').'?_token='.csrf_token() }}'
            }
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
      
    });
</script>




@extends('layout.index')
@if (isset($id))
    @section('title', 'Edit program Tab Details')
@else
    @section('title', 'Add program Tab Details')
@endif
<!-- Dashboard Ecommerce start -->
@section('content')
    <section>
        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-8 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12 d-flex">
                            <h2 class="content-header-title float-start mb-0">
                                @if (isset($id))
                                Edit program Tab
                                @else
                                Add program Tab
                                @endif
                                </h2>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-4 col-12 d-md-block d-none">
                    <div class="mb-1 breadcrumb-right">
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb" style="justify-content: flex-end">
                                <li class="breadcrumb-item"><a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">program Tab</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    @if (isset($id))
                                    Edit program Tab
                                    @else
                                    Add program Tab
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
            <section id="multiple-column-form">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="form"
								 id="categoryform" action="" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="form_type" value="add_edit_program_Tab">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        
                                        {{-- <div class="col-md-5 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip01"> Brand Name <span style="color:red"> *</span></label>
                                                <select class=" form-control form-select " id="brand_id"  name='brand_name'>
                                                    <option value="" >-- Select Brand --</option>
                                                    
                                                    <?php 
                                                    // foreach ($master_brand as $val)
                                                    
                                                    // {
                                                    //     $selected = '';
                                                    //     if(old('brand_name',$brand_id) == $val->id){
                                                    //         $selected = 'selected';
                                                    //     }
                                                    //     ?>
                                                    //     <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->brand_name; ?></option>
                                                    //     <?php 
                                                    // }
                                                    ?>
                            
                                                </select>
                                                @error('brand_name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <h5><small class="text-danger" id="brand_name_err"></small></h5>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-5 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip02"> Program Name<span style="color:red"> *</span> </label>
                                                <select class=" form-control form-select " id="program_id"  name='program_name'>
                                                    <option value="" >-- Select Program --</option>
                                                    
                                                    <?php 
                                                    foreach ($master_program as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('program_name',$program_id) == $val->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->program_name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                @error('program_name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <h5><small class="text-danger" id="program_name_err"></small></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-12">   
                                            <div class="form-group">
                                                <label class="form-label" for="validationTooltip03"> Tab Name<span style="color:red"> *</span> </label>
                                                <select class=" form-control form-select   " id="tab_name"  name='tab_name'>
                                                    <option value="">-- Select Tab --</option>
                                                    
                                                    <?php 
                                                    foreach ($master_tab as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('tab_name',$tab_id) == $val->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $val->id; ?>" <?php echo $selected; ?>><?php echo $val->tab_name; ?></option>
                                                        <?php 
                                                    }
                                                    ?>
                            
                                                </select>
                                                @error('tab_name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <h5><small class="text-danger" id="tab_name_err"></small></h5>
                                            </div>
                                        </div>
                                        
                                   
                                        <div class="col-md-5 col-12">   
                                            <div class="form-group">
                                                <label for="tab_title">Tab Title<span style="color:red"> *</span> </label>
                                                <input type="text" id="tab_title" class="form-control"
                                                    placeholder="Enter Tab Title" name="tab_title"
                                                    value="{{ old('tab_title',$tab_title ) }}" autocomplete="off" />
                                                @error('tab_title')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <h5><small class="text-danger" id="tab_title_err"></small></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-12">   
                                            <div class="form-group">
                                                <label for="order_sort">Order Sort<span style="color:red"> *</span> </label>
                                                <input type="number" id="order_sort" class="form-control"
                                                    placeholder="Enter Order Sort" name="order_sort"
                                                    value="{{ old('order_sort',$sort_order ) }}" autocomplete="off" />
                                                @error('order_sort')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <h5><small class="text-danger" id="order_sort_err"></small></h5>
                                            </div>
                                        </div> 
                                    </div>  

                                    <div class="col-12 text-center mt-1">
                                        <input type="submit" class="btn btn-primary mr-1" onClick="return validation()" value="Submit">
                                        <a href="{{ url('/program_tabs') }}"
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get references to the Tab Name dropdown and Tab Title input
        var tabName = document.getElementById('tab_name');
        var tabTitle = document.getElementById('tab_title');
    
        // Add an event listener to the Tab Name dropdown
        tabName.addEventListener('change', function () {
            // Update the Tab Title input with the selected Tab Name
            tabTitle.value = tabName.options[tabName.selectedIndex].text;
        });
    });
</script>



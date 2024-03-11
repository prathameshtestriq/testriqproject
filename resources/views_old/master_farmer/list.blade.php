@extends('layout.index')
@section('title', 'Program Tab List')

<!-- Dashboard Ecommerce start -->
@section('content')
{{-- <?php dd(json_encode($farmers));?> --}}
<section>
    <div class="content-header row">
        <div class="content-header-left col-md-8 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12 d-flex">
                    <h2 class="content-header-title float-start mb-0">Master Farmer List</h2>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-end col-md-4 col-12 d-md-block d-none">
            <div class="mb-1 breadcrumb-right">
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb" style="justify-content: flex-end">
                        <li class="breadcrumb-item"><a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Master</a>
                        </li>
                        <li class="breadcrumb-item active">Master Farmer List
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
                        <form class="dt_adv_search" action="{{ url('master_farmers') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="search_master_farmer">
                            <div class="card-header w-100 m-0">
                                <div class="row w-150">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-12 col-12">   
                                                <div class="form-group">
                                                    <label class="form-label" for="validationTooltip01" > Country Name: </label><br>
                                                 
                                                    <?php 
                                                    foreach ($master_country as $val)
                                                    
                                                    {
                                                        $selected = '';
                                                        if(old('country_name',$country_name) == $val->id){
                                                            $selected = 'selected';
                                                        } 
                                                        ?>
                                                      
                                                      <a href="{{ url('/master_farmers/fetch_countries', $val->id) }}" class="btn btn-primary p-2"><?php echo $val->country_name; ?></a>
                                                        <?php 
                                                    }
                                                    ?>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                      
                    </div>

                </div>
            </div>
            <!-- Bordered table end -->
        </div>
    </section>
    
@endsection

{{-- <script>
    function selected_country() {
        alert("here");
        var country_id = $('#country_dropdown').val();
        var url = "<?php echo url('/master_farmers/fetch_countries') ?>";
        url = url + '/' + country_id; 
        alert(url);
        $.ajax({
            url: "<?php echo url('/master_farmers/fetch_countries'); ?>/".country_id,
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                country_id: country_id,
            },
            success: function (form_result) {
                var farmers = form_result.farmers;
            //   console.log(farmers);
                var html = `<table class="table table-striped table-bordered"><thead><tr> <th class="text-center">Sr. No</th>
                                <th class="text-left">Brand Name</th>
                                <th class="text-left">Program Name</th>
                                <th class="text-left">Country Name</th>
                                <th class="text-left">State Name</th>
                                <th class="text-left">District Name</th>
                                <th class="text-left">Block Name</th>
                                <th class="text-left">Village Name</th>
                                <th class="text-left">Name</th>
                                <th class="text-left">Agriculture Irrigated</th>
                                <th class="text-left">Agriculture Rainfed</th>
                                <th class="text-left">Cotton Irrigated</th>
                                <th class="text-left">Cotton Rainfed</th>
                                <th class="text-left">Total Agriculture Area</th>
                                <th class="text-left">Total Cotton Area</th>
                                <th class="text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">`;
                          
                if (farmers && farmers.length > 0) {
                    for (var i = 0; i < farmers.length; i++) {   
                        html += `<tr>
                                    <td>${i+1}</td>
                                    <td>${farmers[i].brand_name}</td>
                                    <td>${farmers[i].program_name}</td>
                                    <td>${farmers[i].country_name}</td>
                                    <td>${farmers[i].state_name}</td>
                                    <td>${farmers[i].district_name}</td>
                                    <td>${farmers[i].block_name}</td>
                                    <td>${farmers[i].village_name}</td>        
                                    <td>${farmers[i].name}</td>
                                    <td>${farmers[i].agriculture_irrigated}</td>
                                    <td>${farmers[i].agriculture_rainfed}</td>
                                    <td>${farmers[i].cotton_irrigated}</td>
                                    <td>${farmers[i].cotton_rainfed}</td>
                                    <td>${farmers[i].total_agricultaral_area}</td>
                                    <td>${farmers[i].total_cotton_area}</td>
                                    <td></td>
                                </tr>`;
                    }
                } else {
                    html += `<tr>
                                <td colspan="16" style="text-align:center; color:red;">No Record Found</td>
                            </tr>`;
                }

                html += `</tbody></table>`;

                // Append the HTML content to the specified element with id 'content'
                $('#table_content').html(html);
            },
            error: function (jqXHR, testStatus, error) {
                alert("Page cannot open. Error: " + error);
            },
        });
    }
</script> --}}



@extends('layout.index')
@if (!empty($id))
    @section('title', ' Remittance Management')
@else
    @section('title', ' Remittance Management')
@endif

@section('title', 'Remittance  Create')
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
                                        <h2 class="content-header-title float-left mb-0">
                                            @if (!empty($aReturn['id']))
                                                Edit Remittance  Details
                                            @else
                                                Add Remittance  Details
                                            @endif
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
                                            @if (!empty($aReturn['id']))
                                                Edit Remittance  
                                            @else
                                                Add Remittance 
                                            @endif 
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
                                    <input type="hidden" name="form_type" value="add_edit_remittance_management"
                                        enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                  
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="remittance_name">Remittance Name <span style="color:red;">*</span></label>
                                                <input type="text" id="remittance_name" class="form-control"
                                                    placeholder="Remittance Name" name="remittance_name"  value="{{ old('remittance_name', $remittance_name) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="remittance_name_err"></small></h5>
                                                @error('remittance_name')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="remittance_date">Remittance Date <span style="color:red;">*</span></label>
                                                <input type="date" id="remittance_date" class="form-control"
                                                    placeholder="Remittance Date" name="remittance_date"
                                                    value="{{ old('remittance_date', $remittance_date ? \Carbon\Carbon::parse($remittance_date)->format('Y-m-d') : '') }}"  
                                                    autocomplete="off" />
                                                <h5><small class="text-danger" id="remittance_date_err"></small></h5>
                                                @error('remittance_date')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
 
                                        <div class="col-sm-6 col-12">
                                            <label for="form-control"> Events <span style="color:red;">*</span></label>
                                            <select id="event" name="event" class="form-control select2 form-control">
                                                <option value="">Select  Event</option>
                                                <?php 
                                                    foreach ($EventsData as $value)
                                                    {
                                                        $selected = '';
                                                        if(old('event',$event_id) == $value->id){
                                                            $selected = 'selected';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $value->id; ?>" <?php echo $selected; ?>><?php echo $value->name; ?></option>
                                                        <?php 
                                                    }
                                                ?>
                                            </select>
                                            <h5><small class="text-danger" id="event_err"></small></h5>
                                                @error('event')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="gross_amount">Gross Amount <span style="color:red;">*</span></label>
                                                <input type="text" id="gross_amount" class="form-control"
                                                    placeholder="Gross Amount" name="gross_amount" oninput="validateNumberInput(this)"  value="{{ old('gross_amount', $gross_amount) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="gross_amount_err"></small></h5>
                                                @error('gross_amount')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="service_charge">Service Charge <span style="color:red;">*</span></label>
                                                <input type="text" id="service_charge" class="form-control"
                                                    placeholder="Service Charge" name="service_charge"  oninput="validateNumberInput(this)"   value="{{ old('service_charge', $service_charge) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="service_charge_err"></small></h5>
                                                @error('service_charge')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="Sgst">SGST <span style="color:red;">*</span></label>
                                                <input type="text" id="Sgst" class="form-control"
                                                    placeholder="Sgst" name="Sgst"  value="{{ old('Sgst', $Sgst) }}"   oninput="validateNumberInput(this)"  autocomplete="off" />
                                                <h5><small class="text-danger" id="Sgst_err"></small></h5>
                                                @error('Sgst')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                     
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="Cgst">CGST <span style="color:red;">*</span></label>
                                                <input type="text" id="Cgst" class="form-control"
                                                    placeholder="Cgst" name="Cgst"  value="{{ old('Cgst', $Cgst) }}"  oninput="validateNumberInput(this)"  autocomplete="off" />
                                                <h5><small class="text-danger" id="Cgst_err"></small></h5>
                                                @error('Cgst')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="Igst">IGST <span style="color:red;">*</span></label>
                                                <input type="text" id="Igst" class="form-control"
                                                    placeholder="Igst" name="Igst"  value="{{ old('Igst', $Igst) }}"  oninput="validateNumberInput(this)"  autocomplete="off" />
                                                <h5><small class="text-danger" id="Igst_err"></small></h5>
                                                @error('Igst')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="deductions">Deductions <span style="color:red;">*</span></label>
                                                <input type="text" id="deductions" class="form-control"
                                                    placeholder="Deductions" name="deductions"  value="{{ old('deductions', $deductions) }}"  oninput="validateNumberInput(this)"  autocomplete="off" />
                                                <h5><small class="text-danger" id="deductions_err"></small></h5>
                                                @error('deductions')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
 
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="Tds">Tds <span style="color:red;">*</span></label>
                                                <input type="text" id="Tds" class="form-control"
                                                    placeholder="Tds" name="Tds"  value="{{ old('Tds', $Tds) }}"   oninput="validateNumberInput(this)"  autocomplete="off" />
                                                <h5><small class="text-danger" id="Tds_err"></small></h5>
                                                @error('Tds')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="amount_remitted">Amount Remitted <span style="color:red;">*</span></label>
                                                <input type="text" id="amount_remitted" class="form-control"
                                                    placeholder="Amount Remitted" name="amount_remitted"  oninput="validateNumberInput(this)"   value="{{ old('amount_remitted', $amount_remitted) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="amount_remitted_err"></small></h5>
                                                @error('amount_remitted')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="bank_reference">Bank Reference <span style="color:red;">*</span></label>
                                                <input type="text" id="bank_reference" class="form-control"
                                                    placeholder="Bank Reference" name="bank_reference"  value="{{ old('bank_reference', $bank_reference) }}" autocomplete="off" />
                                                <h5><small class="text-danger" id="bank_reference_err"></small></h5>
                                                @error('bank_reference')
                                                    <span class="error" style="color:red;">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-center mt-1">
                                            <button type="submit" class="btn btn-primary mr-1"
                                                onClick="return validation()">Submit</button>
                                            <a href="{{ url('/remittance_management') }}" type="reset"
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

<script type="text/javascript">
     function validateNumberInput(input) {
        input.value = input.value.replace(/[^0-9.]/g, ''); // Remove non-digit and non-decimal point characters
        if ((input.value.match(/\./g) || []).length > 1) {
            input.value = input.value.slice(0, -1); // Remove additional decimal points if there are more than one
        }
    }
</script>


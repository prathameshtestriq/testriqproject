@extends('layout.index')
@section('title', 'Role Master List')

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
                                    <h2 class="content-header-title float-left mb-0">Access Master List</h2>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end breadcrumb-wrapper">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mr-1">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                    <li class="breadcrumb-item">Access Master</li>
                                    <li class="breadcrumb-item active" aria-current="page">Access Master List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bordered table end -->
    </div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-body">
        <div class="row" id="table-bordered">
            <div class="col-12">
                <div class="card">
                    <div class="card-header w-100">
                        <div class="col-sm-12">
                            <form action="{{ route('role_master.update', $role->id) }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Module Name</th>
                                                <th class="text-center">None</th>
                                                <th class="text-center">Read</th>
                                                <th class="text-center">Write</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($modules as $index => $module)
                                                <tr>
                                                    <td class="text-left">{{ $module->module_name }}</td>
                                                    <td class="text-center">
                                                        <input type="radio" name="access[{{ $module->id }}]" value="0" {{ $module->access == 0 ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="access[{{ $module->id }}]" value="1" {{ $module->access == 1 ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="radio" name="access[{{ $module->id }}]" value="2" {{ $module->access == 2 ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="col-12 text-center mt-1">
                                        <button type="submit" class="btn btn-primary">Submit</button>&nbsp;
                                        <a href="{{ url('/role_master') }}" type="reset"
                                            class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

</section>
@endsection
@extends('layouts.app')

@section('content')


<div class="col-sm-15">
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('zones.index') }}">Main</a>
        </li>
        <li class="breadcrumb-item">Zones</li>
    </ol>
</div>
<h1>Zones</h1>
<div class="custom-box" style="margin-bottom: 50px; margin-top: 20px; border-top: 5px solid #007bff; padding-top: 15px;width">
    <div class="col-sm-15">
        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-primary btn-flat" href="{{ route('zones.create') }}">
                    <i class="fa fa-plus-circle"></i> Create a new zone
                </a>
            </div>
        </div>
    </div>
    <div class="custom-box" style="margin-bottom: 50px; margin-left: 10px; border-left: 5px solid #007bff; padding-top: 5px;">
        <h5><i class="icon fas fa-info"></i> You need to set your domain nameservers to:</h5>
        
        <ul class="gray-background">
            <li>ns1.centos-webpanel.com</li>
            <li>ns2.centos-webpanel.com</li>
            <li>ns3.centos-webpanel.com</li>
            <li>ns4.centos-webpanel.com</li>
            <li>ns5.centos-webpanel.com</li>
        </ul>
    </div>
    <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
            <!-- "Show entries" Section -->
            <div>
                <label>Show
                    <select id="entriesCount" class="custom-select custom-select-sm form-control form-control-sm d-inline-block" style="width: 70px;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                </label>
            </div>
            <!-- "Search" Section -->
            <div>
                <label>Search:
                    <input type="search" id="searchInput" class="form-control form-control-sm d-inline-block w-auto" placeholder="" aria-controls="table_zone">
                </label>
            </div>
        </div>
    </div>
        <table class="table"id="dataTable">
            <thead>
                <tr class="text-white bg-primary">
                    <th>Name</th>
                    <th>Serial</th>
                    <th>User </span></th>
                    <th>Changed</th>
                    <th>Valid </th>
                    <th>Delete</th>
                </tr>
            </thead>
            
                </thead>
                <tbody>
                    @foreach($zones as $index => $zone)
                    <tr style="background-color: {{ $index % 2 == 0 ? '#f2f2f2' : '#ffffff' }};">
                        <td>
                            <a href="{{ route('zones.editzone', ['id' => $zone->id]) }}">
                                <span style="background-color: rgb(0, 132, 255); color: white; padding: 2px 4px; border-radius: 3px;">
                                    {{ $zone->name }}
                                </span>
                            </a>
                        </td>
                        <td>{{ $zone->id }}</td>
                        <td>{{ $zone->user->username ?? 'N/A' }}</td> 
                        <td>
                            <span class="right badge badge-success"><i class="fa fa-check-circle"></i></span>
                        </td>
                        <td>
                            <span class="right badge badge-secondary"><i class="fa fa-times"></i></span>
                        </td>
                        <td>
                            <!-- The form for deleting a zone -->
                            <form action="{{ route('zones.destroy', $zone->id) }}" method="POST" id="deleteForm-{{ $zone->id }}">
                                @csrf
                                @method('DELETE')
                                <!-- Button triggers SweetAlert2 confirmation dialog -->
                                <button type="button" class="btn btn-danger btn-xs" onclick="showDeleteConfirmation({{ $zone->id }})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                
            </table>
          
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers" id="table_zone_paginate">
                  <ul class="pagination">
                    <li class="paginate_button page-item previous disabled" id="table_zone_previous">
                      <a href="#" class="page-link" onclick="changePage('prev')">Previous</a>
                    </li>
                    <li class="paginate_button page-item" id="page-1">
                        <a href="{{ $zones->url(1) }}" class="page-link" onclick="changePage(1)">1</a>
                    </li>
                    <li class="paginate_button page-item" id="page-2">
                      <a href="{{ $zones->url(2) }}" class="page-link" onclick="changePage(2)">2</a>
                    </li>
                    <li class="paginate_button page-item next" id="table_zone_next">
                      <a href="#" class="page-link" onclick="changePage('next')">Next</a>
                    </li>
                  </ul>
                </div>
              </div>
    </div>
</div>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            showConfirmButton: true,
            timer: 5000
        });
    </script>
    @endif
@endsection

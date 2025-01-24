@extends('layouts.app')

@section('content')
<div class="col-sm-15">
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('zones.index') }}">Main</a>
        </li>
        <li class="breadcrumb-item">Viewing zone</li>
    </ol>
    <h1>Viewing zone</h1>
</div>

<div class="custom-box"
    style="margin-bottom: 50px; margin-top: 20px; border-top: 5px solid #007bff; padding-top: 15px;">
    <div class="card-body">
        <!-- Main Content -->
        <div id="page-wrapper">
            <div class="main-page">
                <div class="sign-up-row widget-shadow">
                    <p>Here you can modify your zone's SOA record, or add, edit, or delete resource records</p>

                    <div class="row">
                        <!-- Delete Form -->
                        <div class="col-md-12 text-right mb-3">
                            <form action="{{ route('zones.destroy', $zone->id) }}"
                                method="POST" id="deleteForm-{{ $zone->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-xs"
                                    onclick="showDeleteConfirmation({{ $zone->id }})">
                                    <i class="fa fa-trash"></i> Delete Zone
                                </button>
                            </form>
                        </div>

                        <!-- DNS Zone Test -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3>DNS Zone Test</h3>
                                </div>
                                <div class="card-body">
                                    <pre style="background: #e9ecef; border-radius: 10px; padding: 15px;">
{{ $zone->name }} /IN: loaded serial {{ $zone->id }} OK
                                </pre>
                                </div>
                            </div>
                        </div>

                        <!-- Real Domain Nameservers -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Real Domain Nameservers</h3>
                                </div>
                                <div class="card-body">
                                    <div style="padding: 15px; background: #e9ecef; border-radius: 10px;">
                                        <code>
                                            ns1.digimedia.com<br>
                                            ns2.digimedia.com
                                        </code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form name="form1" method="POST" action="{{ route('zones.update', $zone->id) }}">
                @csrf
                <blade
                    method|(%26%2339%3BPUT%26%2339%3B)%20%3C!--%20Specify%20the%20method%20as%20PUT%20for%20the%20update%20operation%20--%3E%0D />
                <input type="hidden" name="zoneid" id="zoneid" value="{{ $zone->id }}">

                <div class="form-title">

                </div>
                <div class="form-title">
                    <h4>Viewing zone</h4>
                </div>
                <div class="custom-box"
                    style="margin-bottom:50px;margin-top:20px;border-left: 5px solid #007bff; width: 60%; margin-left: 20%;">
                    <div class="row" style="padding-left: 30%">
                        <div class="callout callout-info col-md-8">
                            <h5><i class="icon fas fa-success"></i> You need to set your domain nameservers
                                to:</h5>
                            <div align="left">
                                <span style="font-size: 14px" class="badge bg-secondary">ns1.centos-webpanel.com
                                </span><br>
                                <span style="font-size: 14px" class="badge bg-secondary">ns2.centos-webpanel.com </span>
                                &nbsp;<br>
                                <span style="font-size: 14px" class="badge bg-secondary">ns3.centos-webpanel.com </span>
                                <br>
                                <span style="font-size: 14px" class="badge bg-secondary">ns4.centos-webpanel.com </span>
                                <br>
                                <span style="font-size: 14px" class="badge bg-secondary">ns5.centos-webpanel.com </span>
                                &nbsp;<br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: 2em;">
                    <label for="name" class="col-sm-2 control-label"><strong>Zone/Domain</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="name" name="name" value="{{ $zone->name }}"
                            readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="focusedinput" class="col-sm-2 control-label"><strong>Refresh</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="refresh" name="refresh"
                            value="{{ $zone->refresh }}" placeholder="Refresh">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="focusedinput" class="col-sm-2 control-label"><strong>Retry</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="retry" name="retry" value="{{ $zone->retry }}"
                            placeholder="Retry">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="focusedinput" class="col-sm-2 control-label"><strong>Expire</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="expire" name="expire" value="{{ $zone->expire }}"
                            placeholder="Expire">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="focusedinput" class="col-sm-2 control-label"><strong>Time To Live</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="ttl" name="ttl" value="{{ $zone->ttl }}"
                            placeholder="Time To Live">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="focusedinput" class="col-sm-2 control-label"><strong>Primary NS</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="pri_dns" name="pri_dns"
                            value="{{ $zone->pri_dns }}" placeholder="Primary NS">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="focusedinput" class="col-sm-2 control-label"><strong>Secondary NS</strong></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="sec_dns" name="sec_dns"
                            value="{{ $zone->sec_dns }}" placeholder="Secondary NS">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="owner" class="col-sm-2 control-label"><strong>Owner</strong></label>
                    <div class="col-sm-8">
                        <lable name="owner" id="owner" class="form-control select2">
                            <option value="{{ $zone->user->id }}" selected>
                                {{ $zone->user->username }}
                        </lable>
                        </select>
                    </div>
                </div>


                <div style="text-align: center">
                    <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> save
                        zone</button>
                </div>
            </form>
        </div>
    </div>
    <form name="form2" method="POST" action="{{ route('zones.updateRecords', $zone->id) }}">
        @csrf
        @method('PUT')
        <div class="row" style="margin-top: 2em;">
            <div class="col-sm-12">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-white bg-primary">
                            <th>Host</th>
                            <th>Type</th>
                            <th>Destination</th>
                            <th>Valid</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Start Loop -->
                        @foreach($zone->records as $key => $record)
                            <tr>
                                <td>
                                    <input type="hidden" name="record_id[{{ $key }}]" value="{{ $record->id }}">
                                    <input type="text" name="host[{{ $key }}]" class="form-control"
                                        value="{{ $record->host }}" size="16">
                                </td>
                                <td>
                                    <select name="type[{{ $key }}]" class="form-control">
                                        <option value="A"
                                            {{ $record->type == 'A' ? 'selected' : '' }}>
                                            A</option>
                                        <option value="A6"
                                            {{ $record->type == 'A6' ? 'selected' : '' }}>
                                            A6</option>
                                        <option value="AAAA"
                                            {{ $record->type == 'AAAA' ? 'selected' : '' }}>
                                            AAAA</option>
                                        <option value="AFSDB"
                                            {{ $record->type == 'AAAA' ? 'selected' : '' }}>
                                            AAAA</option>
                                        <option value="CNAME"
                                            {{ $record->type == 'CNAME' ? 'selected' : '' }}>
                                            CNAME</option>
                                        <option value="DNAME"
                                            {{ $record->type == 'DNAME' ? 'selected' : '' }}>
                                            DNAME</option>
                                        <option value="DS"
                                            {{ $record->type == 'DS' ? 'selected' : '' }}>
                                            DS</option>
                                        <option value="LOC"
                                            {{ $record->type == 'LOC' ? 'selected' : '' }}>
                                            LOC</option>
                                        <option value="MX"
                                            {{ $record->type == 'MX' ? 'selected' : '' }}>
                                            MX</option>
                                        <option value="NAPTR"
                                            {{ $record->type == 'NAPTR' ? 'selected' : '' }}>
                                            NAPTR</option>
                                        <option value="NS"
                                            {{ $record->type == 'NS' ? 'selected' : '' }}>
                                            NS</option>
                                        <option value="PTR"
                                            {{ $record->type == 'PTR' ? 'selected' : '' }}>
                                            PTR</option>
                                        <option value="RP"
                                            {{ $record->type == 'RP' ? 'selected' : '' }}>
                                            RP</option>
                                        <option value="SRV"
                                            {{ $record->type == 'SRV' ? 'selected' : '' }}>
                                            SRV</option>
                                        <option value="SSHFP"
                                            {{ $record->type == 'SSHFP' ? 'selected' : '' }}>
                                            SSHFP</option>
                                        <option value="TXT"
                                            {{ $record->type == 'TXT' ? 'selected' : '' }}>
                                            TXT</option>
                                        <option value="WKS"
                                            {{ $record->type == 'WKS' ? 'selected' : '' }}>
                                            WKS</option>
                                        <!-- Add other types here -->
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="destination[{{ $key }}]" class="form-control" size="26"
                                        value="{{ $record->destination }}">
                                </td>
                                <td>
                                    <span
                                        class="right badge {{ $record->valid ? 'badge-success' : 'badge-danger' }}"
                                        style="padding: 3px">
                                        <i
                                            class="fa {{ $record->valid ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    </span>
                                </td>
                                <td>
                                    <input type="checkbox" name="delete[{{ $key }}]" class="">
                                </td>
                            </tr>
                        @endforeach
                        <!-- End Loop -->
                    </tbody>
                </table>

                <hr>
                <h3>New Record</h3>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr class="text-white bg-primary">
                            <th>Host</th>
                            <th>Type</th>
                            <th>Destination</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="newhost" class="form-control" size="16">
                            </td>
                            <td>
                                <select name="newtype" class="form-control">
                                    <option value="A">A</option>
                                    <option value="A">A6</option>
                                    <option value="AAAA">AAAA</option>
                                    <option value="AAAA">AFSDB</option>
                                    <option value="CNAME">CNAME</option>
                                    <option value="DNAME">DNAME</option>
                                    <option value="DS">DS</option>
                                    <option value="LOC">LOC</option>
                                    <option value="MX">MX</option>
                                    <option value="NAPTR">NAPTR</option>
                                    <option value="NS">NS</option>
                                    <option value="PTR">PTR</option>
                                    <option value="RP">RP</option>
                                    <option value="SRV">SRV</option>
                                    <option value="SSHFP">SSHFP</option>
                                    <option value="TXT">TXT</option>
                                    <option value="WKS">WKS</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="newdestination" class="form-control" size="26">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div style="text-align: center">
            <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Save</button>
        </div>
    </form>
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

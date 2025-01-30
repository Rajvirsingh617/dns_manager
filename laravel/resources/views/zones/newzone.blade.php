@extends('layouts.app')

@section('content')


<h1>New Zone</h1>
<div class="custom-box"
    style="margin-bottom: 50px; margin-top: 20px; border-top: 5px solid #007bff; padding-top: 15px;">
    <div class="col-sm-15">
        <div class="card-body">
            <!-- //header-ends -->
            <!-- main content start-->
            <div id="page-wrapper">
                <div class="main-page">
                    <div class="sign-up-row widget-shadow">
                        <div class="form-title">
                            <h4>New zone :</h4>
                        </div>

                        <div class="custom-box"
                            style="margin-bottom: 50px; margin-left: 20px; border-left: 5px solid #007bff; padding-top: 15px;">
                            <h5><i class="icon fas fa-info"></i> You need to set your domain nameservers to:</h5>
                            <ul class="gray-background">
                                @foreach($zones as $zone)
                                    <li>{{ $zone->pri_dns }}</li>
                                    <li>{{ $zone->sec_dns }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Displaying validation errors -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Zone form starts here -->
                        <form action="{{ route('zones.store') }}" method="POST">
                            @csrf
                            @if(auth()->user()->isAdmin())
                                <div class="form-group row">
                                    <label for="user_id" class="col-sm-2 control-label"><strong>User</strong></label>
                                    <div class="col-sm-8">
                                        <select id="user_id" name="user_id" class="form-control">
                                            <option value="">Select User</option>
                                            <optgroup label="Admins">
                                                @foreach($admins as $admin)
                                                    <option value="{{ $admin->id }}"
                                                        {{ old('user_id') == $admin->id ? 'selected' : '' }}>
                                                        {{ $admin->username }}
                                                    </option>
                                                @endforeach
                                            </optgroup>

                                            <optgroup label="Users">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->username }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            @endif


                            <div class="form-group row" style="margin-top: 2em;">
                                <label for="name" class="col-sm-2 control-label"><strong>Zone/Domain</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Domain/Zone Name"
                                        value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="refresh" class="col-sm-2 control-label"><strong>Refresh</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                        value="{{ old('refresh', '28800') }}"
                                        id="refresh" name="refresh" placeholder="Refresh">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="retry" class="col-sm-2 control-label"><strong>Retry</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                        value="{{ old('retry', '7200') }}"
                                        id="retry" name="retry" placeholder="Retry">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="expire" class="col-sm-2 control-label"><strong>Expire</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                        value="{{ old('expire', '1209600') }}"
                                        id="expire" name="expire" placeholder="Expire">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ttl" class="col-sm-2 control-label"><strong>Time To Live</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                        value="{{ old('ttl', '86400') }}"
                                        id="ttl" name="ttl" placeholder="Time To Live">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pri_dns" class="col-sm-2 control-label"><strong>Primary NS</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                        value="{{ old('pri_dns', 'ns1.centos-webpanel.com') }}"
                                        id="pri_dns" name="pri_dns" placeholder="Primary NS">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="sec_dns" class="col-sm-2 control-label"><strong>Secondary
                                        NS</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                        value="{{ old('sec_dns', 'ns2.centos-webpanel.com') }}"
                                        id="sec_dns" name="sec_dns" placeholder="Secondary NS">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="www" class="col-sm-2 control-label"><strong>Web Server IP</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ old('www') }}"
                                        id="www" name="www" placeholder="Web Server IP">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mail" class="col-sm-2 control-label"><strong>Mail Server IP</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ old('mail') }}"
                                        id="mail" name="mail" placeholder="Mail Server IP">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="ftp" class="col-sm-2 control-label"><strong>FTP Server IP</strong></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ old('ftp') }}"
                                        id="ftp" name="ftp" placeholder="FTP Server IP">
                                </div>
                            </div>


                            <!-- Submit Button -->
                            <div class="form-group row">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Add Zone
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="css/select2/select2.min.css" rel="stylesheet">
<script src="js/select2/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $("#owner").select2({
            minimumInputLength: 2,
            ajax: {
                url: 'ajax/usersSelect.php',
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.username,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        $('#owner').on('select2:select', function (e) {
            $("#ownderSelec").val(e.params.data.id);
        });
    });

</script>

<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 18px;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
    }

    .select2-container .select2-selection--single {
        height: 32px;
        line-height: 18px;
    }

</style>
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

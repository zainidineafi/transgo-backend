@extends('layouts/main')

@section('container')
<div class="lime-container">
    <div class="lime-body">
        <div class="container">
            <div class="row">
                <div class="col-xl">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Upt</h5>
                            <p>Isi data dengan lengkap dan tepat</p>
                            <form method="POST" action="{{ route('upts.store') }}" enctype="multipart/form-data">
                                @csrf  
                                <div class="form-group">
                                    <label for="name">Nama</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan Nama" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan Email" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan Password" required minlength="8">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">Tampilkan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address">Alamat</label>
                                    <input type="text" class="form-control" name="address" id="address" placeholder="Alamat" required>
                                </div>

                                    <div class="form-group">
                                        <label for="gender">Jenis Kelamin</label>
                                        <select class="js-states form-control" name="gender" id="gender" style="width: 100%">
                                            <option value="Male">Pria</option>
                                            <option value="Female">Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_number">Nomor Handphone</label>
                                        <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Nomor Handphone" required>
                                    </div>

                                     
                                    <div class="form-group">
                                        <label for="level">Hak Akses</label>
                                        <select class="js-states form-control" name="level" id="level" style="width: 100%" disabled>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" @if($role->name === 'Upt') selected @endif>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>   
                                                                          
                                <div class="form-group">
                                    <label for="image">Avatar</label>
                                    <input class="form-control" type="file" name="image" id="image">
                                </div>

                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
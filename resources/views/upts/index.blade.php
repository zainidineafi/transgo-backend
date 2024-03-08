@extends('layouts/main')

@section('container')
<div class="lime-container">
    <div class="lime-body">
        <div class="container">
            <div class="row">
                <div class="col-xl">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Tabel Data Upt</h5>
                                <form action="">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="search" id="searchInput" placeholder="Masukkan Nama Upt" value="">
                                    </div>
                                </form>
                                <a href="{{ route('upts.create') }}" class="btn btn-primary float-right">Tambah</a>

                            </div>
                        </div>
                        <div id="uptsTable" class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Alamat</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Nomor Handphone</th>
                                        <th>Tanggal Bergabung</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upts as $upt)
                                    <tr>
                                        <td>{{ $upt->id }}</td>
                                        <td>{{ $upt->name }}</td>
                                        <td>{{ $upt->email }}</td>
                                        <td>{{ $upt->address }}</td>
                                        <td>
                                            @if ($upt->gender === 'male')
                                                Laki-laki
                                            @elseif ($upt->gender === 'f    emale')
                                                Perempuan
                                            @endif
                                        </td>
                                        
                                        <td>{{ $upt->phone_number }}</td>
                                        <td>{{ $upt->created_at }}</td>
                                        <td>
                                            <a href="" class="btn btn-primary">Edit</a>
                                            <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                            Delete
                                        </button>
                                        
                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                Are you sure you want to delete this item?
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <form id="deleteForm-{{ $upt->id }}" action="{{ route('upts.destroy', $upt->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
  
                                        </td>
                                    </tr>                                    
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
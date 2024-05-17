@extends('layouts/main')

@section('container')
    <div class="lime-body">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="dashboard-info row">
                                <div class="info-text col-md-6">
                                    @if(Auth::check())
                                    <h5 class="card-title">Welcome back, {{ $user->name }}!</h5>
                                    <p>Your roles:
                                        @foreach($user->getRoleNames() as $role)
                                            {{ $role }}
                                        @endforeach
                                    </p>
                                @endif

                                    <p>Get familiar with dashboard, here are some ways to get started.</p>
                                    <ul>
                                        <li>Check some stats for your website bellow</li>
                                        <li>Sync content to other devices</li>
                                        <li>You now have access to File Manager app.</li>
                                    </ul>
                                    <a href="#" class="btn btn-warning m-t-xs">Learn More</a>
                                </div>
                                <div class="info-image col-md-6"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Grafik Pengguna Daftar</h5>
                            <canvas id="userRegistrationsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Admin</h5>
                            @php
                                // Mengambil jumlah total admin
                                $totalAdmin = \App\Models\User::role('Admin')->count();
                                // Menghitung persentase total admin dari seluruh pengguna
                                $percentAdmin = $totalAdmin > 0 ? ($totalAdmin / \App\Models\User::count()) * 100 : 0;
                            @endphp
                            <h2 class="float-right">{{ $totalAdmin }}</h2>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentAdmin }}%" aria-valuenow="{{ $percentAdmin }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title">Orders</h5>
                            <h2 class="float-right">14.3K</h2>
                            <p>Orders in waitlist</p>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body">
                            <h5 class="card-title">Monthly Profit</h5>
                            <h2 class="float-right">45.6$</h2>
                            <p>For last 30 days</p>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="userRegistrationsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>

        @endsection
 
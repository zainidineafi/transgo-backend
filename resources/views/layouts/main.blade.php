<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Responsive Admin Dashboard Template">
        <meta name="keywords" content="admin,dashboard">
        <meta name="author" content="stacks">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        
        <!-- Title -->
        <title>TransGo</title>

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../assets/plugins/font-awesome/css/all.min.css" rel="stylesheet">
        <link href="../../assets/plugins/toastr/toastr.min.css" rel="stylesheet">

        <link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet">
        <link href="../../assets/plugins/cropper-master/cropper.min.css" rel="stylesheet"> 
        <link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/> 

        <!-- Theme Styles -->
        <link href="../../assets/css/lime.min.css" rel="stylesheet">
        <link href="../../assets/css/custom.css" rel="stylesheet">
        <link href="../../assets/css/upload.css" rel="stylesheet">
        
        <script src="../../assets/js/valid_busstat.js"></script>
        <script src="https://unpkg.com/cropperjs"></script>
   


    </head>
    <body>
        <div class='loader'>
            <div class='spinner-grow text-primary' role='status'>
                <span class='sr-only'>Loading...</span>
            </div>
        </div>

        @include('partials.sidebar')

        
        <div class="lime-header">
            <nav class="navbar navbar-expand-lg">
                <section class="material-design-hamburger navigation-toggle">
                    <a href="javascript:void(0)" class="button-collapse material-design-hamburger__icon">
                        <span class="material-design-hamburger__layer"></span>
                    </a>
                </section>
                <a class="navbar-brand" href="{{ Auth::user()->hasRole('Root') ? route('upts.index') : route('dashboard') }}">Transgo</a>
        


                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="material-icons">keyboard_arrow_down</i>
                </button>
        
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <div class="navbar-text ml-auto d-flex align-items-center">
                                <img src="{{ asset('storage/' . Auth::user()->images) }}" alt="Avatar" class="avatar mr-2">
                                {{ str_word_count(Auth::user()->name) > 1 ? explode(" ", Auth::user()->name)[0] : Auth::user()->name }}
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('profile') }}">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Keluar
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
                
                

                

            </nav>
        </div>
        
        
        <div class="lime-container">
            @yield('container')
        </div>

        <!-- Javascripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../../assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="../../assets/plugins/bootstrap/popper.min.js"></script>
        <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../assets/plugins/cropper-master/cropper.min.js"></script>
        
        <script src="../../assets/plugins/toastr/toastr.min.js"></script>
        <script src="../../assets/plugins/select2/js/select2.full.min.js"></script>
        

        

        <script src="../../assets/js/pages/toastr.js"></script>
       
        <script src="../../assets/js/lime.min.js"></script>
        <script src="../../assets/js/pages/select2.js"></script>

        

        <script src="../../assets/js/custom.js"></script>
        <script src="../../assets/js/search.js"></script>
        <script src="../../assets/js/valid_busstat.js"></script>
        <script src="../../assets/js/disabled.js"></script>
        <script src="../../assets/js/multi_del.js"></script>
        <script src="../../assets/js/status.js"></script>
        <script src="../../assets/js/upload.js"></script>
        <script src="../../assets/js/select.js"></script>
       <!-- Pastikan $userRegistrations telah disertakan sebelum script -->
       @if(Route::currentRouteName() == 'dashboard')
       <script src="../../assets/js/dashboard.js"></script>
        <script>
            var ctx = document.getElementById('userRegistrationsChart').getContext('2d');
            var userRegistrationsData = @json($userRegistrations);

            var dates = userRegistrationsData.map(data => data.date);
            var registrations = userRegistrationsData.map(data => data.registrations);

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'User Registrations',
                        data: registrations,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        @endif


            @if(session('message'))
        <script>    
        toastr.success("{{ Session::get('message') }}");
        </script>
        @endif
    </body>
</html>
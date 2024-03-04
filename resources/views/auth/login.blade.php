
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Responsive Admin Dashboard Template">
        <meta name="keywords" content="admin,dashboard">
        <meta name="author" content="stacks">
        
        <!-- Title -->
        <title>Lime - Responsive Admin Dashboard Template</title>

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/plugins/font-awesome/css/all.min.css" rel="stylesheet">
      
        <!-- Theme Styles -->
        <link href="assets/css/lime.min.css" rel="stylesheet">
        <link href="assets/css/custom.css" rel="stylesheet">

    </head>
    <body class="login-page err-500">
        <div class='loader'>
            <div class='spinner-grow text-primary' role='status'>
                <span class='sr-only'>Loading...</span>
            </div>
        </div>
        <div class="container">
            <div class="login-container">
                <div class="row">
                    <div class="col-lg-4 col-md-5 col-sm-9 lfh">
                        <div class="card login-box">
                            <div class="card-body">
                                <h5 class="card-title">Login</h5>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf                               
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" aria-describedby="emailHelp" placeholder="Enter email" required autofocus>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                
                                    <div class="custom-control custom-checkbox form-group">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                        <label class="custom-control-label" for="remember">Remember me</label>
                                    </div>
                                
                                    @if ($errors->has('error'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('error') }}
                                        </div>
                                    @endif
                                
                                    <a href="#" class="float-left forgot-link">Forgot password?</a>
                                    <button class="btn btn-primary float-right m-l-xxs" type="submit">Login</button>
                                </form>  
                                                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-3.1.0.min.js"></script>
        <script src="assets/plugins/bootstrap/popper.min.js"></script>
        <script src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="assets/js/lime.min.js"></script>
    </body>
</html>
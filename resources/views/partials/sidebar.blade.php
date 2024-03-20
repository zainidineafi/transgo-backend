<div class="lime-sidebar">
    <div class="lime-sidebar-inner slimscroll">
        <ul class="accordion-menu">
            <li class="sidebar-title">
                Apps
            </li>
            @if(auth()->check() && !auth()->user()->hasRole('Root'))
            <li>
                <a href="index.html" class="active"><i class="material-icons">dashboard</i>Dashboard</a>
            </li>
            @endif
            @if(auth()->check() && auth()->user()->hasRole('Root'))
                <li>
                    <a href="{{ route('upts.index') }}"><i class="material-icons">person_outline</i>Management Upt</a>
                </li>
            @endif
            @if(auth()->check() && !auth()->user()->hasRole('Root'))
                <li>
                    <a href="{{ route('admins.index') }}"><i class="material-icons">person_outline</i>Management Admin</a>
                </li>
                <li>
                    <a href=""><i class="material-icons">person_outline</i>Management Terminal</a>
                </li>
                @endif
                @if(auth()->check() && !auth()->user()->hasRole('Root'))
                <li>
                    <a href=""><i class="material-icons">person_outline</i>Management Sopir</a>
                </li>
                <li>
                    <a href=""><i class="material-icons">person_outline</i>Management Kondektur</a>
                </li>
                <li>
                    <a href=""><i class="material-icons">person_outline</i>Management Bus</a>
                </li>
                <li>
                    <a href=""><i class="material-icons">person_outline</i>Jadwal</a>
                </li>
                @endif
            </div>
</div>
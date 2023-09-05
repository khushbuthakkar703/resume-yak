<!-- Header Part -->
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('index') }}">
                <img src="{{ asset('images/logo/logo.png') }}" alt="{{ config('app.name') }}" class="logo">
            </a>
          <span class="icon icon_menu"><i class="fas fa-bars"></i></span>
          <ul class="navbar main_navigation">
            <li class="active_nav has_sub_nav_my_profile"><a href="#"><span class="icon"><i class="fas fa-user-alt"></i></span> My Profile <span class="icon icon_right_blue"><i class="fas fa-caret-down"></i></span></a>
            <div class="my_profile_sub_menu">
              <span class="icon"><i class="fas fa-caret-up"></i></span>
              <ul>
                <li><a id="aContact" href="#">Contact Info</a></li>
                <li><a id="aWork_His" href="#">Work History</a></li>
                <li><a id="aEdu" href="#">Education </a></li>
                <!-- <li><a id="aLogout" href="#">Logout </a></li> -->
                <logout-start></logout-start>
              </ul>
            </div>
            </li>
            <li class='my_resumes'><a href="/builder/contact/#/myresume"><span class="icon"><i class="fas fa-file-alt"></i></span> My Resumes</a></li>
              <!-- resume name dropdoen from vue js  -->
              <resume-name-start></resume-name-start>
          </ul>
        </div>
    </nav>
</header>
<!-- End Header Part -->

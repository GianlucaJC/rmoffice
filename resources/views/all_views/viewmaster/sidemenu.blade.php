  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
	
    <a href="{{ route('main_view') }}" class="brand-link">
      <img src="{{ URL::asset('/') }}dist/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">RM_Office</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
			<img src="{{ URL::asset('/') }}dist/img/avatary.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
		<?php
		 $infouser=Auth::user()->name;
		?>
          <a href="#" class="d-block">{{ $infouser}}</a>
        </div>
      </div>
	  
	  <div class="user-panel mt-3 pb-3 mb-3 d-flex">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
		
			
			  
				 <li class="nav-item">
					  <li class="nav-item">
						<a href="{{route('main_view')}}" class="nav-link">
						  <i class="far fa-circle nav-icon"></i>
						  <p>Vista tabulato</p>
						</a>
					  </li>
				  </li>	
		  
			
				<?php if (1==2) {?>
				<li class="nav-item menu">
					<a href="#" class="nav-link">
					  <i class="nav-icon fas fa-users"></i>
					  <p>Gestione archivi
						<i class="right fas fa-angle-left"></i>
					  </p>
					</a>
					
					<ul class="nav nav-treeview">
					 <li class="nav-item">
						  <li class="nav-item">
							<a href="" class="nav-link">
							  <i class="far fa-circle nav-icon"></i>
							  <p>Gestione Aziende</p>
							</a>
						  </li>
					  </li>
					
					  <li class="nav-item">
						<a href="" class="nav-link">
						  <i class="far fa-circle nav-icon"></i>
						  <p>Definizione Utenti</p>
						</a>
					  </li>
				  

					  <li class="nav-item">
						<a href="" class="nav-link">
						  <i class="far fa-circle nav-icon"></i>
						  <p>Definizione attività</p>
						</a>
					  </li>
					  
					  <li class="nav-item">
						<a href="" class="nav-link">
						  <i class="far fa-circle nav-icon"></i>
						  <p>Categorie Documenti</p>
						</a>
					  </li>


					  <li class="nav-item">
						  <li class="nav-item">
							<a href="" class="nav-link">
							  <i class="far fa-circle nav-icon"></i>
							  <p>Assegnazioni</p>
							</a>
						  </li>
					  </li>
							
					</ul>  
			
				</li>
				<?php } ?>


		  <li class="nav-item">
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					  <li class="nav-item">
						<a href="#" class="nav-link" onclick="event.preventDefault();this.closest('form').submit();">
						  <i class="far fa-circle nav-icon"></i>
						  <p>Logout</p>
						</a>
					  </li>

				</form>	
          </li>
		  
		  </ul>
		
			
			<!--<img style="width:100%" src='{{ URL::asset('/') }}dist/img/rlst.png?ver=1.1'>  !-->
		  </div>
		  

		  
        
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  

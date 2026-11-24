<div class="menu_section">
	<h3>General</h3>
	<ul class="nav side-menu">
	<!-- No Access Control -->
	<li>
		<a href="/report">
			<i class="fa fa-folder-open"></i>
			Report
		</a>
	</li>
	<li>
		<a href="/hospitals">
			<i class="fa fa-hospital-o"></i>
			Hospitals
		</a>
	</li>
	<!-- Logged in Users -->
	@if(Auth::check()) 
	<li>
		<a href="/add_emergency">
			<i class="fa fa-flash"></i>
			Add Mission/Emergency
		</a>
	</li>
	@endif
	
	<li>
		<a>
			<i class="fa fa-phone-square"></i>
			Emergency Call
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			<li>
				<a href="/emergency_call">
					<i class="fa fa-phone"></i>
					Add New Emergency Call
				</a>
			</li>
			<li>
				<a href="/emergency_calls">
					<i class="fa fa-folder-open-o"></i>
					View Emergency Calls
				</a>
			</li>
		</ul>
	</li>
	<!-- Secretariat Sub Menu -->
	@if(Auth::check() && (
			Auth::user()->memberOf('Secretariat Members')
				||
			Auth::user()->memberOf('Statistic')||
			Auth::user()->memberOf('Chef Equipe')

		)
	)
	<li>
		<a>
			<i class="fa fa-group"></i>
			Secretariat
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			@if(
				Auth::check() && (
					Auth::user()->memberOf('Secretariat Members') || Auth::user()->memberOf('Chef Equipe')
				)
			)
			<li>
				<a href="/add_village">
					<i class="fa fa-map-marker"></i>
					Add Village
				</a>
			</li>
			<li>
				<a href="/register" target="_blank">
					<i class="fa fa-plus-circle"></i>
					Add New Register
				</a>
			</li>
			<li>
				<a href="/all_register">
					<i class="fa fa-shopping-cart"></i>
					All Registers
				</a>
			</li>
			@endif
			@if(
				Auth::check() && (
					Auth::user()->memberOf('Statistic')
				)
			)
			<li>
				<a href="/reports">
					<i class="fa fa-list-alt"></i>
					Generate Emergency Reports
				</a>
			</li>
			@endif
		</ul>
	</li>
	@endif

	<!-- Drivers -->
	@if(Auth::check() && ( Auth::user()->driver || Auth::user()->memberOf('Head of Station') ) )
	<li>
		<a>
			<i class="fa fa-automobile"></i>
			Drivers
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			<li>
				<a href="/add_fuel_coupon">
					<i class="fa fa-car"></i>
					Add Fuel Coupon
				</a>
			</li>
			<li>
				<a href="/driver_fuel_logs">
					<i class="fa fa-folder-open"></i>
					Fuel Coupon Report
				</a>
			</li>
			<li>
				<a href="/add_fuel_station">
					<i class="fa fa-building-o"></i>
					Add Fuel Station
				</a>
			</li>
			@if(
				Auth::check() && (
					Auth::user()->memberOf('Drivers Admin')
				)
			)
			<li>
				<a href="/generate_fuel_report">
					<i class="fa fa-list-alt"></i>
					Generate Fuel Coupon Reports
				</a>
			</li>
			<li>
				<a href="/survey_statistics">
					<i class="fa fa-linux"></i>
					Surveys
				</a>
			</li>
			<li>
				<a href="/unfilled_user_surveys">
					<i class="fa fa-linux"></i>
					Unfilled Surveys Count
				</a>
			</li>
			<li>
				<a href="/monthly_negative_comment">
					<i class="fa fa-linux"></i>
					Negative Comments
				</a>
			</li>
			<li>
				<a href="/monthly_positive_comment">
					<i class="fa fa-linux"></i>
					Positive Comments
				</a>
			</li>
			<li>
				<a href="/car_checkup_all">
					<i class="fa fa-tasks"></i>
					Add/Edit Car Checkup
				</a>
			</li>
			<li>
				<a href="/car_checkup_result">
					<i class="fa fa-tasks"></i>
					Car Checkups
				</a>
			</li>
			@endif
		</ul>
	</li>
	@endif
	
	<!-- Edj -->
	@if(Auth::check())
	<li>
		<a>
			<i class="fa fa-money"></i>
			Equipe de Jour
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			<li>
				<a href="/add_edj_shifts">
					<i class="fa fa-check"></i>
					Equipe de Jour
				</a>
			</li>
			@if(
				Auth::check() && (
					Auth::user()->memberOf('Edj Admin')
				)
			)
			<li>
				<a href="/edj_reports">
					<i class="fa fa-list-alt"></i>
					Generate Edj Reports
				</a>
			</li>
			@endif
		</ul>
	</li>
	@endif

	<!-- Material -->
	@if(Auth::check() && (
		Auth::user()->memberOf('Equipment Members')
	))
	<li>
		<a>
			<i class="fa fa-paste"></i>
			Material
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			<li>
				<a href="/allMaterial">
					<i class="fa fa-bar-chart"></i>
					Material Stock
				</a>
			</li>
			<li>
				<a href="/kioskMaterial">
					<i class="fa fa-bar-chart"></i>
					Kiosk Material
				</a>
			</li>
			<li>
				<a href="/allMaterialMobile">
					<i class="fa fa-mobile"></i>
					Mobile Application
				</a>
			</li>
		</ul>
	</li>
	@endif

	<!-- Maneuver -->
	@if(Auth::check() && (
		Auth::user()->memberOf('Chef Equipe')
			||
		Auth::user()->memberOf('Maneuver Admin')
			||
		Auth::user()->memberOf('Head of Station')
	))
	<li>
		<a>
			<i class="fa fa-bookmark"></i>
			Maneuver
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			<li>
				<a href="/maneuvers">
					<i class="fa fa-thumbs-o-up"></i>
					All Maneuvers
				</a>
			</li>
			<li>
				<a href="/maneuver_results">
					<i class="fa fa-search"></i>
					Maneuver Results
				</a>
			</li>
			@if(
				Auth::check() && (
					Auth::user()->memberOf('Maneuver Admin')
						||
					Auth::user()->memberOf('Head of Station')
				)
			)
			<li>
				<a href="/build_maneuver" target="_blank">
					<i class="fa fa-plus-square"></i>
					New Maneuver
				</a>
			</li>
			@endif
		</ul>
	</li>
	@endif
	@if(Auth::check() && (
		Auth::user()->memberOf('Chef Equipe')))
	<li>
		<a href="/reset_pass">
			<i class="fa fa-unlock"></i>
			Reset User Password
		</a>
	</li>
	<li>
		<a href="/send_message">
			<i class="fa fa-paper-plane"></i>
			Send Message
		</a>
	</li>
	@endif
	@if(Auth::check() && (Auth::user()->memberOf('IT Members')))
	<li>
		<a href="/system_funcitonalities">
			<i class="fa fa-unlock"></i>
			System Functionality
		</a>
	</li>
	@endif

	<!-- Points Menu -->
	<li>
		<a>
			<i class="fa fa-blind"></i>
			Points
			<span class="fa fa-chevron-down"></span>
		</a>
		<ul class="nav child_menu">
			<li>
				<a href="/points">
					<i class="fa fa-graduation-cap"></i>
					Point System Grid
				</a>
			</li>
			<li>
				<a href="/view_points">
					<i class="fa fa-cog"></i>
					View my Points
				</a>
			</li>
			@if(Auth::check() && (
					Auth::user()->memberOf('Points Admin') ||
					Auth::user()->memberOf('Chef Equipe')
				)
			)
			<li>
				<a href="/manage_points">
					<i class="fa fa-cog"></i>
					Manage EMT's Points
				</a>
			</li>
			@endif
			@if(Auth::check() && (
					Auth::user()->memberOf('Points Admin') ||
					Auth::user()->memberOf('Secretariat Admin')
				)
			)
			<li>
				<a href="/manage_point_actions">
					<i class="fa fa-cog"></i>
					Manage Point System
				</a>
			</li>
			@endif
		</ul>
	</li>


</div>
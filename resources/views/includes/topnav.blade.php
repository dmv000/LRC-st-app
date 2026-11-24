<nav>
@if(Auth::check())
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <ul class="nav navbar-nav navbar-right">
                <li class="dropdown" role="presentation" style="margin-right: 60px;">
		<a class="dropdown-toggle info-number" aria-expanded="false" href="user_surveys" >
                    <i class="fa fa-check-square-o"></i>
                    <span class="badge bg-green">@if(Auth::check()) {{ Auth::user()->surveys() }} @endif</span>
                  </a>
                </li>
                <li class="dropdown" role="presentation" style="margin-right: 60px;">
                  <a class="dropdown-toggle info-number" aria-expanded="false" href="javascript:;" data-toggle="dropdown">
                    <i class="fa fa-envelope-o"></i>
                    <span class="badge bg-green">@if(Auth::check()) {{ count(Auth::user()->unread_news()->get()) }} @endif</span>
                  </a>
                  <ul class="dropdown-menu list-unstyled msg_list" id="menu1" role="menu">

@forelse(Auth::user()->news()->get() as $news)
                    <li>
                      <a href="/notification?id={{ $news->id }}">
                        <span>
			  <span>&nbsp;</span>
                          <span class="time">{{ $news->time() }}</span>
                        </span>
                        <span class="message">
                          {{ $news->message() }}
                        </span>
                      </a>
                    </li>
@empty  
@endforelse              
                    <li>
                      <div class="text-center">
                        <a>
                          <strong>See All Alerts</strong>
                          <i class="fa fa-angle-right"></i>
                        </a>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
@endif
            </nav>
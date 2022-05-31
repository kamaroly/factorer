<aside class="bg-gray-800 main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <ul class="sidebar-menu">
            <li class="{{ (Request::is('/') ? 'text-gray-100 active' : '') }} treeview">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('home') }}">
                <i class="fa fa-dashboard"></i>
                <span>{{ trans('navigations.dashboard') }}</span>
              </a>
            </li>
            <li class="{{ (Request::is('members*') ? 'text-gray-100 active' : '') }} treeview">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('members.index') }}">
                <i class="fa fa-users"></i>
                <span>{{ trans('navigations.members') }}</span>
                 {{-- <i class="fa fa-angle-left pull-right"></i> --}}
              </a>
             {{-- <ul class="treeview-menu bg-gray-700 {{ (Request::is('members*') ? 'text-gray-100 active' : '') }}">
                <li  class="{{ (Request::is('members*') ? 'text-gray-100 active' : '') }} treeview">
                  <a class="ml-6 mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('members.index') }}">
                   {{ trans('navigations.members') }}
                  </a>
                </li>
                <li  class="{{ (Request::is('members*emergencies*') ? 'text-gray-100 active' : '') }} treeview">
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('members.index') }}?emergencies=true">
                   {{ trans('navigations.emergency_loan_members') }}
                  </a>
                </li>
              </ul> --}}
            </li>
            <li class="{{ (session()->get('contributions-type') === 'BULK_WITHDRAW') ? 'text-gray-100 active' : '' }} treeview">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="#" id="closing">
                <i class="fa fa-lock"></i>
                <span>{{ trans('navigations.closing') }}</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
               <ul class="treeview-menu bg-gray-700 {{ (session()->get('contributions-type') === 'BULK_WITHDRAW') ? 'text-gray-100 active' : '' }}">
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('contributions.interests') }}?contribution-type=BULK_WITHDRAW">
                    {{ trans('navigations.bulk_withdraw') }}
                  </a>
                </li>
              </ul>
            </li>
            <li class="{{ (in_array(session()->get('contributions-type'),['ANNUAL_INTEREST','OTHER']) ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('contributions.index') }}">
                <i class="fa fa-th"></i> <span>{{ trans('navigations.contributions_and_savings') }}</span>
                 <i class="fa fa-angle-left pull-right"></i>
              </a>
               <ul class="treeview-menu bg-gray-700 {{ (in_array(session()->get('contributions-type'),['ANNUAL_INTEREST','OTHER']) ? 'text-gray-100 active' : '') }}">
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('contributions.interests') }}?contribution-type=OTHER">
                    {{ trans('navigations.contributions') }}
                  </a>
                   <i class="fa fa-angle-left pull-right"></i>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('contributions.interests') }}?contribution-type=ANNUAL_INTEREST">
                    {{ trans('navigations.annual_interest') }}
                  </a>
                   <i class="fa fa-angle-left pull-right"></i>
                </li>
              </ul>
            </li>
            <li class="{{ (Request::is('loan*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="#">
                <i class="fa fa-money"></i>
                <span>{{ trans('navigations.loans') }}</span>
               <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu bg-gray-700 {{ (Request::is('loan*') ? 'class="`text-gray-100"' : '') }}">
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('loans.index') }}">
                   {{ trans('navigations.give_loan') }}
                  </a>
                </li>
                 <li >
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('loan.blocked') }}">
                   {{ trans('navigations.unblock_loan') }}
                  </a>
                </li>
                <li >
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('loan.pending') }}">
                   {{ trans('navigations.pending_loans') }}
                  </a>
                </li>
              </ul>
            </li>
           <li class="{{ (Request::is('regularisation*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('regularisation.index') }}">
                <i class="fa fa-level-up"></i>
                <span>{{ trans('navigations.regularisation') }}</span>
              </a>
            </li>
            <li class="{{ (Request::is('refund*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('refunds.index') }}">
                <i class="fa fa-undo"></i>
                <span>{{ trans('navigations.refund') }}</span>
               <i class="fa fa-angle-left pull-right"></i>
              </a>

               <ul class="treeview-menu bg-gray-700 {{ (Request::is('refund*') ? 'text-gray-100 active' : '') }}">
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('refunds.index') }}?refund-loan-type=EMERGENCY_LOAN">
                      {{ trans('navigations.emergency_refund') }}
                  </a>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('refunds.index') }}?refund-loan-type=NON_EMERGENCY_LOAN">
                      {{ trans('navigations.other_refund') }}
                  </a>
                </li>
              </ul>
            </li>
            <li class="{{ (Request::is('correction*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                   href="{{ route('corrections.loan.index') }}">
                <i class="fa fa-check"></i>
                <span>{{ trans('navigations.correction') }}</span>
               <i class="fa fa-angle-left pull-right"></i>
              </a>
               <ul class="treeview-menu bg-gray-700 {{ (Request::is('correction*') ? 'text-gray-100 active' : '') }}">
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('corrections.loan.index') }}">
                      {{ trans('navigations.loan_correction') }}
                  </a>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('corrections.refund.index') }}">
                      {{ trans('navigations.refund_correction') }}
                  </a>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('corrections.contributions.index') }}">
                      {{ trans('navigations.contribution_corrections') }}
                  </a>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('corrections.approvals') }}">
                      {{ trans('navigations.correction_approvals') }}
                  </a>
                </li>
              </ul>
            </li>
             <li class="{{ (Request::is('reconciliations*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                   href="#">
                <i class="fa fa-exchange"></i>
                <span>{{ trans('navigations.reconciliations') }}</span>
               </i>
              </a>
              <ul class="treeview-menu bg-gray-700 {{ (Request::is('contributions*') ? 'text-gray-100 active' : '') }}">
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('reconciliations.refunds.index') }}">
                      {{ trans('navigations.refunds') }}
                  </a>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('reconciliations.contributions.index') }}">
                      {{ trans('navigations.contributions_reco') }}
                  </a>
                </li>
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('reconciliations.bank.index') }}">
                      {{ trans('navigations.bank_reco') }}
                  </a>
                </li>
                <!--<li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" 
                     href="{{ route('reconciliations.account.index') }}">
                      {{ trans('navigations.account_reco') }}
                  </a>
                </li>-->
              </ul>
            </li>
            <li class="{{ (Request::is('accounting*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('accounting.index') }}">
                <i class="fa fa-briefcase"></i>
                <span>{{ trans('navigations.accounting') }}</span>
                </a>

            </li>
          <!--  <li class="{{ (Request::is('leave*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{!! route('leaves.index') !!}">
                <i class="fa fa-calendar"></i> <span>{{ trans('navigations.leaves') }}</span>
              </a>
            </li>
            <li class="{{ (Request::is('items*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{!! route('items.index') !!}">
               <i class="fa fa-tasks"></i></i> <span>{{ trans('navigations.items') }}</span>
              </a>
            </li> 
            -->
            <li class="{{ (Request::is('report*') ? 'text-gray-100 active' : '') }} ">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{!! route('reports.index') !!}">
                <i class="fa fa-bar-chart"></i> <span>{{ trans('navigations.reports') }}</span>
              </a>
            </li>
            <li class="treeview">
              <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="#">
                <i class="fa fa-gears"></i>
                 <span>{{ trans('navigations.settings') }}</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu bg-gray-700">
                @if (Sentry::check() && Sentry::getUser()->hasAccess('admin'))
                <li {{ (Request::is('users*') ? 'class="`text-gray-100"' : '') }}>
                <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('ceb.settings.users.index') }}">
                <i class="fa fa-user"></i>{{ trans('navigations.user') }}
                </a>
                </li>
                <li {{ (Request::is('groups*') ? 'class="`text-gray-100"' : '') }}>
                <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ action('\\Sentinel\Controllers\GroupController@index') }}">
                 <i class="fa fa-users"></i> {{ trans('navigations.groups') }}
                </a>
                </li>
              @endif
                <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{!! route('settings.institution.index') !!}">
                    <i class="fa fa-home"></i> 
                      {{ trans('navigations.institutions') }}
                  </a>
                </li>
                 <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{!! route('settings.bank.index') !!}">
                    <i class="fa fa-bank"></i> 
                      {{ trans('navigations.banks') }}
                  </a>
                </li>

                <li>
                   <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{!! route('settings.accountingplan.index') !!}">
                     <i class="fa fa-book"></i> 
                       {{ trans('navigations.accounting_plan') }}
                   </a>
                </li>
               <!-- <li>
                  <a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="#">
                     <i class="fa fa-lock"></i> 
                      {{ trans('navigations.closing_exercise') }}
                  </a>
                </li>  ---->
              </ul>
            </li>
            <li><a class="mt-1 ml-3 lg:mt-1 flex items-center px-2 -mx-2 py-1 hover:text-gray-300 text-xl font-medium text-gray-400" href="{{ route('logs') }}"><i class="fa fa-question-circle"></i>
            <span>{{ trans('navigations.logs') }}</span></a>
            </li>
            <li class="header"> </li>
          </ul>
          
        </section>
        <!-- /.sidebar -->
      </aside>
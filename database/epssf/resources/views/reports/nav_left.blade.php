<table class="table bring-up nth-2-center">
<tbody>
  <tr>
      <td class="sideNavSubHeader">{{ trans('reports.contracts') }} 
      </td>
  </tr>
      <tr>
        <td class="sideNav">
        <a class="sideNavLink" href="{{ route('reports.contracts.saving') }}">{{ trans('contracts.saving') }}</a>
        </td>
      </tr>
      <tr>
        <td class="sideNav">
        <a class="sideNavLink" href="{{ route('reports.contracts.loan') }}">{{ trans('contracts.loan') }}</a>
        </td>
      </tr>
      <tr>
        <td class="sideNav">
        <a class="sideNavLink" href="{{ route('reports.contracts.ordinaryloan') }}">{{ trans('contracts.ordinaryloan') }}</a>
        </td>
      </tr>
  <tr>
        <td class="sideNav">
        <a class="sideNavLink" href="{{ route('reports.contracts.socialloan') }}">{{ trans('contracts.socialloan') }}</a>
        </td>
      </tr>
</tbody></table>